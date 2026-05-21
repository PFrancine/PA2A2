package models

import (
	"time"

	"github.com/google/uuid"
)

type Evenement struct {
	ID          uuid.UUID  `json:"id"`
	Titre       string     `json:"titre"`
	Description string     `json:"description"`
	TypeEvent   string     `json:"type_event"`
	Prix        float64    `json:"prix"`
	DateEvent   time.Time  `json:"date_event"`
	Lieu        *string    `json:"lieu,omitempty"`
	IDAnimateur *uuid.UUID `json:"id_utilisateur,omitempty"`
}

type CreateEvenementRequest struct {
	Titre       string    `json:"titre"`
	Description string    `json:"description"`
	TypeEvent   string    `json:"type_event"`
	Prix        float64   `json:"prix"`
	DateEvent   time.Time `json:"date_event"`
	Lieu        *string   `json:"lieu,omitempty"`
}

type Inscription struct {
	IDEvenement    uuid.UUID `json:"id_evenement"`
	IDUtilisateur  uuid.UUID `json:"id_utilisateur"`
	StatutPaiement string    `json:"statut_paiement"`
}
