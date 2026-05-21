package handlers

import (
	"encoding/json"
	"net/http"
	"strings"
	"upcycleconnect/db"
	"upcycleconnect/models"
	"upcycleconnect/utils"

	"golang.org/x/crypto/bcrypt"
)

func Register(w http.ResponseWriter, r *http.Request) {
	var req models.RegisterRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "impossible de décoder le JSON", http.StatusBadRequest)
		return
	}

	errs := validateRegister(req)
	if len(errs) > 0 {
		encoded, _ := json.Marshal(errs)
		w.Header().Set("Content-Type", "application/json")
		http.Error(w, string(encoded), http.StatusBadRequest)
		return
	}

	if db.EmailExists(req.Email) {
		http.Error(w, "cet email est déjà utilisé", http.StatusConflict)
		return
	}

	hashed, err := bcrypt.GenerateFromPassword([]byte(req.MotDePasse), bcrypt.DefaultCost)
	if err != nil {
		http.Error(w, "erreur lors du hachage du mot de passe", http.StatusInternalServerError)
		return
	}

	id, err := db.CreateUtilisateur(req, string(hashed))
	if err != nil {
		http.Error(w, "erreur lors de la création du compte", http.StatusInternalServerError)
		return
	}

	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusCreated)
	json.NewEncoder(w).Encode(map[string]string{"id": id.String()})
}

func Login(w http.ResponseWriter, r *http.Request) {
	var req models.LoginRequest
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "impossible de décoder le JSON", http.StatusBadRequest)
		return
	}

	if strings.TrimSpace(req.Email) == "" || strings.TrimSpace(req.MotDePasse) == "" {
		http.Error(w, "email et mot de passe requis", http.StatusBadRequest)
		return
	}

	user, err := db.GetUtilisateurByEmail(req.Email)
	if err != nil {
		http.Error(w, "erreur serveur", http.StatusInternalServerError)
		return
	}
	if user == nil {
		http.Error(w, "identifiants invalides", http.StatusUnauthorized)
		return
	}

	if !user.Actif {
		http.Error(w, "compte désactivé", http.StatusForbidden)
		return
	}

	if err := bcrypt.CompareHashAndPassword([]byte(user.MotDePasse), []byte(req.MotDePasse)); err != nil {
		http.Error(w, "identifiants invalides", http.StatusUnauthorized)
		return
	}

	token, err := utils.GenerateToken(user.ID, user.IDRole)
	if err != nil {
		http.Error(w, "erreur lors de la génération du token", http.StatusInternalServerError)
		return
	}

	// Ne pas renvoyer le mot de passe dans la réponse
	user.MotDePasse = ""

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(models.LoginResponse{
		Token: token,
		User:  *user,
	})
}

func validateRegister(req models.RegisterRequest) []string {
	var errs []string

	if len(req.Nom) < 2 || len(req.Nom) > 100 {
		errs = append(errs, "Le nom doit contenir entre 2 et 100 caractères")
	}
	if len(req.Prenom) < 2 || len(req.Prenom) > 100 {
		errs = append(errs, "Le prénom doit contenir entre 2 et 100 caractères")
	}
	if !strings.Contains(req.Email, "@") || len(req.Email) > 150 {
		errs = append(errs, "Email invalide")
	}
	if len(req.MotDePasse) < 8 {
		errs = append(errs, "Le mot de passe doit contenir au moins 8 caractères")
	}
	if req.IDRole == 0 {
		errs = append(errs, "Le rôle est requis")
	}
	if req.IDSite == 0 {
		errs = append(errs, "Le site est requis")
	}

	return errs
}
