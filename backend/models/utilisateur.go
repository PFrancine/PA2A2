package models

import (
	"github.com/google/uuid"
)

type Utilisateur struct {
	ID         uuid.UUID `json:"id"`
	Nom        string    `json:"nom"`
	Prenom     string    `json:"prenom"`
	Email      string    `json:"email"`
	MotDePasse string    `json:"mot_de_passe,omitempty"`
	Actif      bool      `json:"actif"`
	IDRole     int       `json:"id_role"`
	IDSite     int       `json:"id_site"`
}

type RegisterRequest struct {
	Nom        string `json:"nom"`
	Prenom     string `json:"prenom"`
	Email      string `json:"email"`
	MotDePasse string `json:"mot_de_passe"`
	IDRole     int    `json:"id_role"`
	IDSite     int    `json:"id_site"`
}

type LoginRequest struct {
	Email      string `json:"email"`
	MotDePasse string `json:"mot_de_passe"`
}

type LoginResponse struct {
	Token string      `json:"token"`
	User  Utilisateur `json:"user"`
}
