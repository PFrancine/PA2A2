package db

import (
	"database/sql"
	"fmt"
	"upcycleconnect/models"

	"github.com/google/uuid"
)

func GetUtilisateurByEmail(email string) (*models.Utilisateur, error) {
	var u models.Utilisateur
	row := Conn.QueryRow(
		"SELECT id_utilisateur, nom, prenom, email, mot_de_passe, actif, id_role, id_site FROM UTILISATEUR WHERE email = $1",
		email,
	)
	err := row.Scan(&u.ID, &u.Nom, &u.Prenom, &u.Email, &u.MotDePasse, &u.Actif, &u.IDRole, &u.IDSite)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}
		return nil, fmt.Errorf("GetUtilisateurByEmail : %v", err)
	}
	return &u, nil
}

func GetUtilisateurById(id uuid.UUID) (*models.Utilisateur, error) {
	var u models.Utilisateur
	row := Conn.QueryRow(
		"SELECT id_utilisateur, nom, prenom, email, mot_de_passe, actif, id_role, id_site FROM UTILISATEUR WHERE id_utilisateur = $1",
		id,
	)
	err := row.Scan(&u.ID, &u.Nom, &u.Prenom, &u.Email, &u.MotDePasse, &u.Actif, &u.IDRole, &u.IDSite)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}
		return nil, fmt.Errorf("GetUtilisateurById : %v", err)
	}
	return &u, nil
}

func CreateUtilisateur(u models.RegisterRequest, hashedPassword string) (uuid.UUID, error) {
	var newID uuid.UUID
	err := Conn.QueryRow(
		`INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, actif, id_role, id_site)
		 VALUES ($1, $2, $3, $4, TRUE, $5, $6)
		 RETURNING id_utilisateur`,
		u.Nom, u.Prenom, u.Email, hashedPassword, u.IDRole, u.IDSite,
	).Scan(&newID)
	if err != nil {
		return uuid.Nil, fmt.Errorf("CreateUtilisateur : %v", err)
	}
	return newID, nil
}

func EmailExists(email string) bool {
	u, _ := GetUtilisateurByEmail(email)
	return u != nil
}
