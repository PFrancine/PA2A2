package main

import (
	"log"
	"net/http"
	"upcycleconnect/db"
	"upcycleconnect/handlers"
	"upcycleconnect/middleware"

	"github.com/joho/godotenv"
)

func main() {
	if err := godotenv.Load(); err != nil {
		log.Fatal("Erreur chargement .env : " + err.Error())
	}

	db.Conn = db.NewDB()

	// ── Auth ──────────────────────────────────────────────────────────────
	http.HandleFunc("POST /register", handlers.Register)
	http.HandleFunc("POST /login", handlers.Login)

	// ── Annonces (public) ─────────────────────────────────────────────────
	http.HandleFunc("GET /annonces", handlers.GetAnnonces)
	http.HandleFunc("GET /annonces/{id}", handlers.GetAnnonceById)

	// ── Annonces (authentifié) ────────────────────────────────────────────
	http.HandleFunc("GET /annonces/mes-annonces", middleware.Auth(handlers.GetMesAnnonces))
	http.HandleFunc("POST /annonces", middleware.Auth(handlers.CreateAnnonce))
	http.HandleFunc("DELETE /annonces/{id}", middleware.Auth(handlers.DeleteAnnonce))

	// ── Annonces (admin — id_role = 1) ────────────────────────────────────
	http.HandleFunc("PATCH /annonces/{id}/statut", middleware.RequireRole(1, handlers.UpdateStatutAnnonce))

	// TODO — à implémenter dans les prochaines itérations :
	// GET    /evenements
	// POST   /evenements             (animateur/admin)
	// POST   /evenements/{id}/inscription  (particulier)
	// GET    /conteneurs
	// POST   /depots                 (particulier, déclenche envoi code)
	// PATCH  /depots/{id}/statut     (admin)
	// GET    /projets
	// POST   /projets                (professionnel)
	// POST   /projets/{id}/etapes    (professionnel)

	log.Println("Serveur UpcycleConnect lancé sur http://localhost:8080")
	log.Fatal(http.ListenAndServe(":8080", nil))
}
