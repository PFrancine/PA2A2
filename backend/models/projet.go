package models

import (
	"time"

	"github.com/google/uuid"
)

type Projet struct {
	ID              uuid.UUID  `json:"id"`
	Titre           string     `json:"titre"`
	Description     string     `json:"description"`
	DateCreation    time.Time  `json:"date_creation"`
	Statut          string     `json:"statut"`
	IDProfessionnel *uuid.UUID `json:"id_professionnel,omitempty"`
}

type CreateProjetRequest struct {
	Titre           string     `json:"titre"`
	Description     string     `json:"description"`
	DateCreation    *time.Time `json:"date_creation,omitempty"`
	IDProfessionnel *uuid.UUID `json:"id_professionnel,omitempty"`
}

type CreateEtapeRequest struct {
	Description string     `json:"description"`
	DateEtape   *time.Time `json:"date_etape,omitempty"`
	IDProjet    uuid.UUID  `json:"id_projet"`
}
