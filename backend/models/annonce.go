package models

import (
	"time"

	"github.com/google/uuid"
)

type Annonce struct {
	ID              uuid.UUID `json:"id"`
	Titre           string    `json:"titre"`
	Description     string    `json:"description"`
	Etat            string    `json:"etat"`
	PoidsKg         *float64  `json:"poids_kg,omitempty"`
	Materiaux       *string   `json:"materiaux,omitempty"`
	TypeAnnonce     string    `json:"type_annonce"`
	Prix            float64   `json:"prix"`
	Statut          string    `json:"statut"`
	DatePublication time.Time `json:"date_publication"`
	IDUtilisateur   uuid.UUID `json:"id_utilisateur"`
}

type CreateAnnonceRequest struct {
	Titre       string   `json:"titre"`
	Description string   `json:"description"`
	Etat        string   `json:"etat"`
	PoidsKg     *float64 `json:"poids_kg,omitempty"`
	Materiaux   *string  `json:"materiaux,omitempty"`
	TypeAnnonce string   `json:"type_annonce"`
	Prix        float64  `json:"prix"`
}
