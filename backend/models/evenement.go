package models

import "time"

type Evenement struct {
	ID          int       `json:"id"`
	Titre       string    `json:"titre"`
	Description string    `json:"description"`
	TypeEvent   string    `json:"type_event"`
	Prix        float64   `json:"prix"`
	DateEvent   time.Time `json:"date_event"`
	Lieu        *string   `json:"lieu,omitempty"`
	IDAnimateur *string   `json:"id_animateur,omitempty"`
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
	IDEvenement    int    `json:"id_evenement"`
	IDUtilisateur  string `json:"id_utilisateur"`
	StatutPaiement string `json:"statut_paiement"`
}
