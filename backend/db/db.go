package db

import (
	"database/sql"
	"fmt"
	"os"

	_ "github.com/lib/pq"
)

var Conn *sql.DB

func NewDB() *sql.DB {
	sqlInfo := fmt.Sprintf(
		"host=%s port=%s user=%s password=%s dbname=%s sslmode=disable",
		os.Getenv("DB_HOST"),
		os.Getenv("DB_PORT"),
		os.Getenv("DB_USER"),
		os.Getenv("DB_PASSWORD"),
		os.Getenv("DB_NAME"),
	)

	conn, err := sql.Open("postgres", sqlInfo)
	if err != nil {
		panic(err.Error())
	}

	if err := conn.Ping(); err != nil {
		panic("impossible de joindre la base de données : " + err.Error())
	}

	fmt.Println("Connecté à la base de données !")
	return conn
}
