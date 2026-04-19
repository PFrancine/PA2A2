package handlers

import (
	"encoding/json"
	"net/http"
	"strings"
	"upcycleconnect/db"
	"upcycleconnect/middleware"
	"upcycleconnect/models"
	"upcycleconnect/utils"

	"github.com/google/uuid"
)

var etatsValides = map[string]bool{
	"TRES_BON": true, "BON": true, "A_REPARER": true, "POUR_PIECES": true,
}
var typesValides = map[string]bool{
	"DON": true, "VENTE": true,
}
var materiauxValides = map[string]bool{
	"BOIS": true, "METAL": true, "PLASTIQUE": true, "AUTRE": true,
}
var statutsValides = map[string]bool{
	"EN_ATTENTE": true, "VALIDE": true, "REFUSE": true,
}

// GET /annonces — public, liste les annonces validées
func GetAnnonces(w http.ResponseWriter, r *http.Request) {
	annonces, err := db.GetAnnonces()
	if err != nil {
		http.Error(w, "erreur de récupération des annonces", http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(annonces)
}

// GET /annonces/{id} — public
func GetAnnonceById(w http.ResponseWriter, r *http.Request) {
	id, err := uuid.Parse(r.PathValue("id"))
	if err != nil {
		http.Error(w, "id invalide", http.StatusBadRequest)
		return
	}

	annonce, err := db.GetAnnonceById(id)
	if err != nil {
		http.Error(w, "erreur de récupération de l'annonce", http.StatusInternalServerError)
		return
	}
	if annonce == nil {
		http.Error(w, "annonce non trouvée", http.StatusNotFound)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(annonce)
}

// GET /annonces/mes-annonces — authentifié, liste ses propres annonces
func GetMesAnnonces(w http.ResponseWriter, r *http.Request) {
	claims := r.Context().Value(middleware.ClaimsKey).(*utils.JWTClaims)

	annonces, err := db.GetAnnoncesByUtilisateur(claims.IDUtilisateur)
	if err != nil {
		http.Error(w, "erreur de récupération des annonces", http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(annonces)
}

// POST /annonces — authentifié (particulier)
func CreateAnnonce(w http.ResponseWriter, r *http.Request) {
	claims := r.Context().Value(middleware.ClaimsKey).(*utils.JWTClaims)

	var req models.CreateAnnonceRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "impossible de décoder le JSON", http.StatusBadRequest)
		return
	}

	errs := validateAnnonce(req)
	if len(errs) > 0 {
		encoded, _ := json.Marshal(errs)
		w.Header().Set("Content-Type", "application/json")
		http.Error(w, string(encoded), http.StatusBadRequest)
		return
	}

	id, err := db.CreateAnnonce(req, claims.IDUtilisateur)
	if err != nil {
		http.Error(w, "erreur lors de la création de l'annonce", http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]string{"id": id.String()})
}

// PATCH /annonces/{id}/statut — admin uniquement (validation/refus)
func UpdateStatutAnnonce(w http.ResponseWriter, r *http.Request) {
	id, err := uuid.Parse(r.PathValue("id"))
	if err != nil {
		http.Error(w, "id invalide", http.StatusBadRequest)
		return
	}

	annonce, err := db.GetAnnonceById(id)
	if err != nil {
		http.Error(w, "erreur de récupération de l'annonce", http.StatusInternalServerError)
		return
	}
	if annonce == nil {
		http.Error(w, "annonce non trouvée", http.StatusNotFound)
		return
	}

	var body struct {
		Statut string `json:"statut"`
	}
	if err := json.NewDecoder(r.Body).Decode(&body); err != nil {
		http.Error(w, "impossible de décoder le JSON", http.StatusBadRequest)
		return
	}
	if !statutsValides[body.Statut] {
		http.Error(w, "statut invalide (EN_ATTENTE, VALIDE, REFUSE)", http.StatusBadRequest)
		return
	}

	if err := db.UpdateStatutAnnonce(id, body.Statut); err != nil {
		http.Error(w, "erreur lors de la mise à jour du statut", http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
}

// DELETE /annonces/{id} — propriétaire ou admin
func DeleteAnnonce(w http.ResponseWriter, r *http.Request) {
	claims := r.Context().Value(middleware.ClaimsKey).(*utils.JWTClaims)

	id, err := uuid.Parse(r.PathValue("id"))
	if err != nil {
		http.Error(w, "id invalide", http.StatusBadRequest)
		return
	}

	annonce, err := db.GetAnnonceById(id)
	if err != nil {
		http.Error(w, "erreur de récupération de l'annonce", http.StatusInternalServerError)
		return
	}
	if annonce == nil {
		http.Error(w, "annonce non trouvée", http.StatusNotFound)
		return
	}

	// Seul le propriétaire ou un admin (id_role=1) peut supprimer
	if annonce.IDUtilisateur != claims.IDUtilisateur && claims.IDRole != 1 {
		http.Error(w, "accès interdit", http.StatusForbidden)
		return
	}

	if err := db.DeleteAnnonce(id); err != nil {
		http.Error(w, "erreur lors de la suppression", http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusNoContent)
}

func validateAnnonce(req models.CreateAnnonceRequest) []string {
	var errs []string

	if len(strings.TrimSpace(req.Titre)) < 3 || len(req.Titre) > 200 {
		errs = append(errs, "Le titre doit contenir entre 3 et 200 caractères")
	}
	if len(strings.TrimSpace(req.Description)) < 10 {
		errs = append(errs, "La description doit contenir au moins 10 caractères")
	}
	if !etatsValides[req.Etat] {
		errs = append(errs, "État invalide (TRES_BON, BON, A_REPARER, POUR_PIECES)")
	}
	if !typesValides[req.TypeAnnonce] {
		errs = append(errs, "Type d'annonce invalide (DON, VENTE)")
	}
	if req.TypeAnnonce == "VENTE" && req.Prix <= 0 {
		errs = append(errs, "Le prix doit être supérieur à 0 pour une vente")
	}
	if req.Materiaux != nil && !materiauxValides[*req.Materiaux] {
		errs = append(errs, "Matériaux invalide (BOIS, METAL, PLASTIQUE, AUTRE)")
	}
	if req.PoidsKg != nil && *req.PoidsKg < 0 {
		errs = append(errs, "Le poids ne peut pas être négatif")
	}

	return errs
}
