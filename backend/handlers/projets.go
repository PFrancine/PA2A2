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

// GET /projets — public
func GetProjets(w http.ResponseWriter, r *http.Request) {
	projets, err := db.GetProjets()
	if err != nil {
		http.Error(w, "erreur de récupération des événements", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(projets)
}

// GET /projets/{id} — public
func GetProjetById(w http.ResponseWriter, r *http.Request) {
	id, err := uuid.Parse(r.PathValue("id"))
	if err != nil {
		http.Error(w, "id invalide", http.StatusBadRequest)
		return
	}
	projet, err := db.GetProjetById(id)
	if err != nil {
		http.Error(w, "erreur de récupération de l'événement", http.StatusInternalServerError)
		return
	}
	if projet == nil {
		http.Error(w, "événement non trouvé", http.StatusNotFound)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(projet)
}

// POST /projets — animateur (id_role=4) ou admin (id_role=1)
func CreateProjet(w http.ResponseWriter, r *http.Request) {
	claims := r.Context().Value(middleware.ClaimsKey).(*utils.JWTClaims)

	if claims.IDRole != 1 && claims.IDRole != 4 {
		http.Error(w, "accès interdit", http.StatusForbidden)
		return
	}

	var req models.CreateProjetRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "impossible de décoder le JSON", http.StatusBadRequest)
		return
	}
	errs := validateProjet(req)
	if len(errs) > 0 {
		encoded, _ := json.Marshal(errs)
		w.Header().Set("Content-Type", "application/json")
		http.Error(w, string(encoded), http.StatusBadRequest)
		return
	}
	id, err := db.CreateProjet(req, claims.IDUtilisateur)
	if err != nil {
		http.Error(w, "erreur lors de la création de l'événement", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]uuid.UUID{"id": id})
}

func CreateEtape(w http.ResponseWriter, r *http.Request) {
	claims := r.Context().Value(middleware.ClaimsKey).(*utils.JWTClaims)

	if claims.IDRole != 1 && (claims.IDRole != 4 && claims.IDUtilisateur != uuid.MustParse(r.PathValue("id"))) {
		http.Error(w, "accès interdit", http.StatusForbidden)
		return
	}

	var req models.CreateEtapeRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "impossible de décoder le JSON", http.StatusBadRequest)
		return
	}
	errs := validateEtape(req)
	if len(errs) > 0 {
		encoded, _ := json.Marshal(errs)
		w.Header().Set("Content-Type", "application/json")
		http.Error(w, string(encoded), http.StatusBadRequest)
		return
	}
	id, err := db.CreateEtapeProjet(req, uuid.MustParse(r.PathValue("id")))
	if err != nil {
		http.Error(w, "erreur lors de la création de l'étape", http.StatusInternalServerError)
		return
	}
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]uuid.UUID{"id": id})
}

func validateProjet(req models.CreateProjetRequest) []string {
	var errs []string
	if len(strings.TrimSpace(req.Titre)) < 3 || len(req.Titre) > 200 {
		errs = append(errs, "Le titre doit contenir entre 3 et 200 caractères")
	}
	if len(strings.TrimSpace(req.Description)) < 10 {
		errs = append(errs, "La description doit contenir au moins 10 caractères")
	}
	if req.DateCreation.IsZero() {
		errs = append(errs, "La date de création est requise")
	}
	if req.IDProfessionnel == nil {
		errs = append(errs, "L'ID du professionnel est requis")
	}
	return errs
}

func validateEtape(req models.CreateEtapeRequest) []string {
	var errs []string
	if len(strings.TrimSpace(req.Description)) < 10 {
		errs = append(errs, "La description doit contenir au moins 10 caractères")
	}
	if req.DateEtape.IsZero() {
		errs = append(errs, "La date de l'étape est requise")
	}
	return errs
}
