SQL:
a voir :

Table langues
table pour gerer les keys conteneur



Poubelle :

CREATE TABLE notification (
    id_notification SERIAL PRIMARY KEY,
    message TEXT NOT NULL,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    id_utilisateur INT NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE upcycling_score (
    id_score SERIAL PRIMARY KEY,
    score_total INT DEFAULT 0,
    dechets_evites_kg DECIMAL(10,2),
    co2_economise DECIMAL(10,2),
    id_utilisateur INT UNIQUE NOT NULL,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

