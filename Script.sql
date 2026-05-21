-- CREATE TYPE _enum AS ENUM ('', '');

CREATE TYPE type_event_enum AS ENUM ('FORMATION', 'ATELIER', 'CONFERENCE');
CREATE TYPE materiaux_enum AS ENUM ('BOIS', 'METAL', 'PLASTIQUE','AUTRE');
CREATE TYPE type_annonce_enum AS ENUM ('DON', 'VENTE');
CREATE TYPE etat_enum AS ENUM ('TRES_BON', 'BON', 'A_REPARER', 'POUR_PIECES');
CREATE TYPE statut_projet_enum AS ENUM ('EN_COURS', 'TERMINE', 'ABANDONNE');
CREATE TYPE statut_depot_enum AS ENUM ('EN_ATTENTE', 'VALIDE', 'RECUPE_PRO');
CREATE TYPE statut_paiement_enum AS ENUM ('EN_ATTENTE', 'REUSSI', 'ECHOUE', 'REMBOURSE');
CREATE TYPE statut_annonce_enum AS ENUM ('EN_ATTENTE', 'VALIDE', 'REFUSE');


CREATE TABLE SITE (
    id_site SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    localisation VARCHAR(150)
);

CREATE TABLE ROLE (
    id_role SERIAL PRIMARY KEY,
    nom_role VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE UTILISATEUR (
    id_utilisateur uuid DEFAULT gen_random_uuid() PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    actif BOOLEAN DEFAULT TRUE,
    id_role INT NOT NULL REFERENCES ROLE(id_role),
    id_site INT REFERENCES SITE(id_site)
);

CREATE TABLE OFFRE (
    id_offre SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    duree_mois INT NOT NULL
);

CREATE TABLE ABONNEMENT (
    id_abonnement SERIAL PRIMARY KEY,
    date_debut DATE NOT NULL,
    date_fin DATE,
    id_utilisateur uuid REFERENCES UTILISATEUR(id_utilisateur),
    id_offre INT NOT NULL REFERENCES OFFRE(id_offre)
);

CREATE TABLE ANNONCE (
    id_annonce uuid DEFAULT gen_random_uuid() PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    etat etat_enum NOT NULL,
    poids_kg DECIMAL(10,3),
    materiaux materiaux_enum,
    type_annonce type_annonce_enum NOT NULL,
    prix DECIMAL(10,2) DEFAULT 0,
    statut statut_annonce_enum NOT NULL DEFAULT 'EN_ATTENTE',
    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_utilisateur uuid REFERENCES UTILISATEUR(id_utilisateur)
);

CREATE TABLE CONTENEUR (
    id_conteneur SERIAL PRIMARY KEY,
    localisation VARCHAR(200) NOT NULL,
    plein BOOL DEFAULT FALSE,
    code_acces VARCHAR(100) UNIQUE NOT NULL  -- A voir avec une table pour gerer les keys si pertinent
);

CREATE TABLE EVENEMENT (
    id_evenement SERIAL PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    type_event type_event_enum NOT NULL,
    prix DECIMAL(10,2) NOT NULL DEFAULT 0,
    date_event TIMESTAMP NOT NULL,
    lieu VARCHAR(150),
    id_animateur uuid REFERENCES UTILISATEUR(id_utilisateur)
);

CREATE TABLE PARTICIPATION_EVENEMENT (
    id_evenement INT NOT NULL REFERENCES EVENEMENT(id_evenement),
    id_utilisateur uuid REFERENCES UTILISATEUR(id_utilisateur),
    statut_paiement statut_paiement_enum DEFAULT 'EN_ATTENTE',
    PRIMARY KEY (id_utilisateur, id_evenement)
);

CREATE TABLE DEPOT (
    id_depot SERIAL PRIMARY KEY,
    id_annonce uuid REFERENCES ANNONCE(id_annonce),
    id_conteneur INT NOT NULL REFERENCES CONTENEUR(id_conteneur),
    code_unique VARCHAR(100) UNIQUE NOT NULL,
    statut statut_depot_enum DEFAULT 'EN_ATTENTE'
);

CREATE TABLE PROJET (
    id_projet SERIAL PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut statut_projet_enum DEFAULT 'EN_COURS',
    id_professionnel uuid REFERENCES UTILISATEUR(id_utilisateur)
);

CREATE TABLE ETAPE_PROJET (
    id_etape SERIAL PRIMARY KEY,
    description TEXT NOT NULL,
    date_etape TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_projet INT NOT NULL REFERENCES PROJET(id_projet)
);

CREATE TABLE TRANSACTION (
    id_transaction SERIAL PRIMARY KEY,
    id_annonce uuid REFERENCES ANNONCE(id_annonce),
    id_acheteur uuid REFERENCES UTILISATEUR(id_utilisateur),
    montant DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2) NOT NULL,
    statut_paiement statut_paiement_enum DEFAULT 'EN_ATTENTE',
    date_transaction TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


---Insert

INSERT INTO ROLE (nom_role) VALUES
    ('ADMIN'),
    ('PARTICULIER'),
    ('PROFESSIONNEL'),
    ('ANIMATEUR');


INSERT INTO SITE (nom, localisation) VALUES
    ('Siège - Lafayette', '174 rue La Fayette, Paris 10e'),
    ('Annexe 11e', 'Paris 11e'),
    ('Annexe 13e', 'Paris 13e'),
    ('Annexe 16e', 'Paris 16e'),
    ('Bourg-la-Reine', 'Bourg-la-Reine'),
    ('Ivry', 'Ivry-sur-Seine'),
    ('Montreuil', 'Montreuil');


INSERT INTO OFFRE (nom, prix, duree_mois) VALUES
    ('Freemium', 0.00, 1),
    ('Premium Mensuel', 15.00, 1);


-- Utilisateur admin par défaut (mot de passe : Admin1234! — à changer)
-- Le hash de "Admin1234!" avec bcrypt
INSERT INTO UTILISATEUR (nom, prenom, email, mot_de_passe, actif, id_role, id_site) VALUES
    ('Admin', 'UpcycleConnect', 'admin@upcycleconnect.fr', 'a refaire', TRUE, 1, 1);

