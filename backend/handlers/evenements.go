package handlers

import (
	"encoding/json"
	"net/http"
	"strconv"
	"strings"
	"upcycleconnect/db"
	"upcycleconnect/middleware"
	"upcycleconnect/models"
	"upcycleconnect/utils"
)

var typesEventValides = map[string]bool{
	"FORMATION": true, "ATELIER": true, "CONFERENCE": true,
}

// GET /evenements — public
func GetEvenements(w http.ResponseWriter, r *http.Request) {
	evenements, err := db.GetEvenements()
	if err != nil {
		http.Error(w, "erreur de récupération des événements", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(evenements)
}

// GET /evenements/{id} — public
func GetEvenementById(w http.ResponseWriter, r *http.Request) {
	id, err := strconv.Atoi(r.PathValue("id"))
	if err != nil {
		http.Error(w, "id invalide", http.StatusBadRequest)
		return
	}
	evenement, err := db.GetEvenementById(id)
	if err != nil {
		http.Error(w, "erreur de récupération de l'événement", http.StatusInternalServerError)
		return
	}
	if evenement == nil {
		http.Error(w, "événement non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(evenement)
}

// POST /evenements — animateur (id_role=4) ou admin (id_role=1)
func CreateEvenement(w http.ResponseWriter, r *http.Request) {
	claims := r.Context().Value(middleware.ClaimsKey).(*utils.JWTClaims)

	if claims.IDRole != 1 && claims.IDRole != 4 {
		http.Error(w, "accès interdit", http.StatusForbidden)
		return
	}

	var req models.CreateEvenementRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "impossible de décoder le JSON", http.StatusBadRequest)
		return
	}
	errs := validateEvenement(req)
	if len(errs) > 0 {
		encoded, _ := json.Marshal(errs)
		w.Header().Set("Content-Type", "application/json")
		http.Error(w, string(encoded), http.StatusBadRequest)
		return
	}
	id, err := db.CreateEvenement(req, claims.IDUtilisateur)
	if err != nil {
		http.Error(w, "erreur lors de la création de l'événement", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]int{"id": id})
}

// POST /evenements/{id}/inscription — authentifié
func InscrireEvenement(w http.ResponseWriter, r *http.Request) {
	claims := r.Context().Value(middleware.ClaimsKey).(*utils.JWTClaims)
	id, err := strconv.Atoi(r.PathValue("id"))
	if err != nil {
		http.Error(w, "id invalide", http.StatusBadRequest)
		return
	}
	evenement, err := db.GetEvenementById(id)
	if err != nil {
		http.Error(w, "erreur de récupération de l'événement", http.StatusInternalServerError)
		return
	}
	if evenement == nil {
		http.Error(w, "événement non trouvé", http.StatusNotFound)
		return
	}
	if err := db.InscrireUtilisateur(id, claims.IDUtilisateur); err != nil {
		if err.Error() == "déjà inscrit" {
			http.Error(w, "vous êtes déjà inscrit à cet événement", http.StatusConflict)
			return
		}
		http.Error(w, "erreur lors de l'inscription", http.StatusInternalServerError)
		return
	}
	w.WriteHeader(http.StatusCreated)
}

// GET /evenements/{id}/inscriptions — admin uniquement
func GetInscriptions(w http.ResponseWriter, r *http.Request) {
	id, err := strconv.Atoi(r.PathValue("id"))
	if err != nil {
		http.Error(w, "id invalide", http.StatusBadRequest)
		return
	}
	inscriptions, err := db.GetInscriptionsByEvenement(id)
	if err != nil {
		http.Error(w, "erreur de récupération des inscriptions", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(inscriptions)
}

func validateEvenement(req models.CreateEvenementRequest) []string {
	var errs []string
	if len(strings.TrimSpace(req.Titre)) < 3 || len(req.Titre) > 200 {
		errs = append(errs, "Le titre doit contenir entre 3 et 200 caractères")
	}
	if len(strings.TrimSpace(req.Description)) < 10 {
		errs = append(errs, "La description doit contenir au moins 10 caractères")
	}
	if !typesEventValides[req.TypeEvent] {
		errs = append(errs, "Type d'événement invalide (FORMATION, ATELIER, CONFERENCE)")
	}
	if req.Prix < 0 {
		errs = append(errs, "Le prix ne peut pas être négatif")
	}
	if req.DateEvent.IsZero() {
		errs = append(errs, "La date de l'événement est requise")
	}
	return errs
}
