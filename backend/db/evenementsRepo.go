package db

import (
	"database/sql"
	"fmt"
	"upcycleconnect/models"

	"github.com/google/uuid"
)

func GetEvenements() ([]models.Evenement, error) {
	var evenements []models.Evenement
	rows, err := Conn.Query(
		`SELECT id_evenement, titre, description, type_event, prix, date_event, lieu, id_animateur
		 FROM EVENEMENT ORDER BY date_event ASC`,
	)
	if err != nil {
		return nil, fmt.Errorf("GetEvenements : %v", err)
	}
	defer rows.Close()

	for rows.Next() {
		var e models.Evenement
		err := rows.Scan(
			&e.ID, &e.Titre, &e.Description, &e.TypeEvent,
			&e.Prix, &e.DateEvent, &e.Lieu, &e.IDAnimateur,
		)
		if err != nil {
			return nil, fmt.Errorf("GetEvenements scan : %v", err)
		}
		evenements = append(evenements, e)
	}
	if err = rows.Err(); err != nil {
		return nil, fmt.Errorf("GetEvenements rows : %v", err)
	}
	return evenements, nil
}

func GetEvenementById(id uuid.UUID) (*models.Evenement, error) {
	var e models.Evenement
	row := Conn.QueryRow(
		`SELECT id_evenement, titre, description, type_event, prix, date_event, lieu, id_animateur
		 FROM EVENEMENT WHERE id_evenement = $1`,
		id,
	)
	err := row.Scan(
		&e.ID, &e.Titre, &e.Description, &e.TypeEvent,
		&e.Prix, &e.DateEvent, &e.Lieu, &e.IDAnimateur,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}
		return nil, fmt.Errorf("GetEvenementById : %v", err)
	}
	return &e, nil
}

func CreateEvenement(req models.CreateEvenementRequest, idAnimateur uuid.UUID) (uuid.UUID, error) {
	var newID uuid.UUID
	err := Conn.QueryRow(
		`INSERT INTO EVENEMENT (titre, description, type_event, prix, date_event, lieu, id_animateur)
		 VALUES ($1, $2, $3, $4, $5, $6, $7)
		 RETURNING id_evenement`,
		req.Titre, req.Description, req.TypeEvent,
		req.Prix, req.DateEvent, req.Lieu, idAnimateur,
	).Scan(&newID)
	if err != nil {
		return uuid.Nil, fmt.Errorf("CreateEvenement : %v", err)
	}
	return newID, nil
}

func InscrireUtilisateur(idEvenement uuid.UUID, idUtilisateur uuid.UUID) error {
	var count int
	err := Conn.QueryRow(
		`SELECT COUNT(*) FROM PARTICIPATION_EVENEMENT
		 WHERE id_evenement = $1 AND id_utilisateur = $2`,
		idEvenement, idUtilisateur,
	).Scan(&count)
	if err != nil {
		return fmt.Errorf("InscrireUtilisateur check : %v", err)
	}
	if count > 0 {
		return fmt.Errorf("déjà inscrit")
	}

	_, err = Conn.Exec(
		`INSERT INTO PARTICIPATION_EVENEMENT (id_evenement, id_utilisateur, statut_paiement)
		 VALUES ($1, $2, 'EN_ATTENTE')`,
		idEvenement, idUtilisateur,
	)
	if err != nil {
		return fmt.Errorf("InscrireUtilisateur : %v", err)
	}
	return nil
}

func GetInscriptionsByEvenement(idEvenement uuid.UUID) ([]models.Inscription, error) {
	var inscriptions []models.Inscription
	rows, err := Conn.Query(
		`SELECT id_evenement, id_utilisateur, statut_paiement
		 FROM PARTICIPATION_EVENEMENT WHERE id_evenement = $1`,
		idEvenement,
	)
	if err != nil {
		return nil, fmt.Errorf("GetInscriptionsByEvenement : %v", err)
	}
	defer rows.Close()

	for rows.Next() {
		var i models.Inscription
		err := rows.Scan(&i.IDEvenement, &i.IDUtilisateur, &i.StatutPaiement)
		if err != nil {
			return nil, fmt.Errorf("GetInscriptionsByEvenement scan : %v", err)
		}
		inscriptions = append(inscriptions, i)
	}
	if err = rows.Err(); err != nil {
		return nil, fmt.Errorf("GetInscriptionsByEvenement rows : %v", err)
	}
	return inscriptions, nil
}
