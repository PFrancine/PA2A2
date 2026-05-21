package db

import (
	"database/sql"
	"fmt"
	"upcycleconnect/models"

	"github.com/google/uuid"
)

func GetAnnonces() ([]models.Annonce, error) {
	var annonces []models.Annonce
	rows, err := Conn.Query(
		`SELECT id_annonce, titre, description, etat, poids_kg, materiaux,
		        type_annonce, prix, statut, date_publication, id_utilisateur
		 FROM ANNONCE
		 WHERE statut = 'VALIDE'
		 ORDER BY date_publication DESC`,
	)
	if err != nil {
		return nil, fmt.Errorf("GetAnnonces : %v", err)
	}
	defer rows.Close()

	for rows.Next() {
		var a models.Annonce
		err := rows.Scan(
			&a.ID, &a.Titre, &a.Description, &a.Etat,
			&a.PoidsKg, &a.Materiaux, &a.TypeAnnonce,
			&a.Prix, &a.Statut, &a.DatePublication, &a.IDUtilisateur,
		)
		if err != nil {
			return nil, fmt.Errorf("GetAnnonces scan : %v", err)
		}
		annonces = append(annonces, a)
	}
	if err = rows.Err(); err != nil {
		return nil, fmt.Errorf("GetAnnonces rows : %v", err)
	}
	return annonces, nil
}

func GetAnnonceById(id uuid.UUID) (*models.Annonce, error) {
	var a models.Annonce
	row := Conn.QueryRow(
		`SELECT id_annonce, titre, description, etat, poids_kg, materiaux,
		        type_annonce, prix, statut, date_publication, id_utilisateur
		 FROM ANNONCE WHERE id_annonce = $1`,
		id,
	)
	err := row.Scan(
		&a.ID, &a.Titre, &a.Description, &a.Etat,
		&a.PoidsKg, &a.Materiaux, &a.TypeAnnonce,
		&a.Prix, &a.Statut, &a.DatePublication, &a.IDUtilisateur,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}
		return nil, fmt.Errorf("GetAnnonceById : %v", err)
	}
	return &a, nil
}

func GetAnnoncesByUtilisateur(idUtilisateur uuid.UUID) ([]models.Annonce, error) {
	var annonces []models.Annonce
	rows, err := Conn.Query(
		`SELECT id_annonce, titre, description, etat, poids_kg, materiaux,
		        type_annonce, prix, statut, date_publication, id_utilisateur
		 FROM ANNONCE
		 WHERE id_utilisateur = $1
		 ORDER BY date_publication DESC`,
		idUtilisateur,
	)
	if err != nil {
		return nil, fmt.Errorf("GetAnnoncesByUtilisateur : %v", err)
	}
	defer rows.Close()

	for rows.Next() {
		var a models.Annonce
		err := rows.Scan(
			&a.ID, &a.Titre, &a.Description, &a.Etat,
			&a.PoidsKg, &a.Materiaux, &a.TypeAnnonce,
			&a.Prix, &a.Statut, &a.DatePublication, &a.IDUtilisateur,
		)
		if err != nil {
			return nil, fmt.Errorf("GetAnnoncesByUtilisateur scan : %v", err)
		}
		annonces = append(annonces, a)
	}
	if err = rows.Err(); err != nil {
		return nil, fmt.Errorf("GetAnnoncesByUtilisateur rows : %v", err)
	}
	return annonces, nil
}

func CreateAnnonce(req models.CreateAnnonceRequest, idUtilisateur uuid.UUID) (uuid.UUID, error) {
	var newID uuid.UUID
	err := Conn.QueryRow(
		`INSERT INTO ANNONCE (titre, description, etat, poids_kg, materiaux, type_annonce, prix, statut, id_utilisateur)
		 VALUES ($1, $2, $3, $4, $5, $6, $7, 'EN_ATTENTE', $8)
		 RETURNING id_annonce`,
		req.Titre, req.Description, req.Etat,
		req.PoidsKg, req.Materiaux, req.TypeAnnonce,
		req.Prix, idUtilisateur,
	).Scan(&newID)
	if err != nil {
		return uuid.Nil, fmt.Errorf("CreateAnnonce : %v", err)
	}
	return newID, nil
}

func UpdateStatutAnnonce(id uuid.UUID, statut string) error {
	_, err := Conn.Exec(
		"UPDATE ANNONCE SET statut = $1 WHERE id_annonce = $2",
		statut, id,
	)
	if err != nil {
		return fmt.Errorf("UpdateStatutAnnonce : %v", err)
	}
	return nil
}

func DeleteAnnonce(id uuid.UUID) error {
	_, err := Conn.Exec("DELETE FROM ANNONCE WHERE id_annonce = $1", id)
	if err != nil {
		return fmt.Errorf("DeleteAnnonce : %v", err)
	}
	return nil
}

func AnnonceIdExists(id uuid.UUID) bool {
	a, _ := GetAnnonceById(id)
	return a != nil
}
