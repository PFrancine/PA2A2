package db

import (
	"database/sql"
	"fmt"
	"upcycleconnect/models"

	"github.com/google/uuid"
)

func GetProjets() ([]models.Projet, error) {
	var Projets []models.Projet
	rows, err := Conn.Query(
		`SELECT id_Projet, titre, description, date_creation, statut, id_animateur
		 FROM Projet ORDER BY date_event ASC`,
	)
	if err != nil {
		return nil, fmt.Errorf("GetProjets : %v", err)
	}
	defer rows.Close()

	for rows.Next() {
		var e models.Projet
		err := rows.Scan(
			&e.ID, &e.Titre, &e.Description, &e.DateCreation,
			&e.Statut, &e.IDProfessionnel,
		)
		if err != nil {
			return nil, fmt.Errorf("GetProjets scan : %v", err)
		}
		Projets = append(Projets, e)
	}
	if err = rows.Err(); err != nil {
		return nil, fmt.Errorf("GetProjets rows : %v", err)
	}
	return Projets, nil
}

func GetProjetById(id uuid.UUID) (*models.Projet, error) {
	var e models.Projet
	row := Conn.QueryRow(
		`SELECT id_Projet, titre, description, date_creation, statut, id_animateur
		 FROM Projet WHERE id_Projet = $1`,
		id,
	)
	err := row.Scan(
		&e.ID, &e.Titre, &e.Description, &e.DateCreation,
		&e.Statut, &e.IDProfessionnel,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}
		return nil, fmt.Errorf("GetProjetById : %v", err)
	}
	return &e, nil
}

func CreateProjet(req models.CreateProjetRequest, idProfessionnel uuid.UUID) (uuid.UUID, error) {
	var newID uuid.UUID
	err := Conn.QueryRow(
		`INSERT INTO PROJET (titre, description, date_creation, id_animateur)
		 VALUES ($1, $2, $3, $4)
		 RETURNING id_projet`,
		req.Titre, req.Description, req.DateCreation, idProfessionnel,
	).Scan(&newID)
	if err != nil {
		return uuid.Nil, fmt.Errorf("CreateProjet : %v", err)
	}
	return newID, nil
}

func CreateEtapeProjet(req models.CreateEtapeRequest, idProfessionnel uuid.UUID) (uuid.UUID, error) {
	var newID uuid.UUID
	err := Conn.QueryRow(
		`INSERT INTO ETAPE_PROJET (description, date_etape, id_animateur)
		 VALUES ($1, $2, $3)
		 RETURNING id_etape`,
		req.Description, req.DateEtape, idProfessionnel,
	).Scan(&newID)
	if err != nil {
		return uuid.Nil, fmt.Errorf("CreateEtapeProjet : %v", err)
	}
	return newID, nil
}
