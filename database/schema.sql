DROP DATABASE IF EXISTS camwater_db;
CREATE DATABASE camwater_db;
\c camwater_db;

CREATE TABLE abonnes (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    ville VARCHAR(50) NOT NULL,
    quartier VARCHAR(255) NOT NULL,
    numero_compteur VARCHAR(255) NOT NULL UNIQUE,
    type_abonnement VARCHAR(50) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE abonnes ADD CONSTRAINT check_ville CHECK (ville IN ('Yaoundé', 'Douala', 'Bafoussam', 'Garoua'));
ALTER TABLE abonnes ADD CONSTRAINT check_type CHECK (type_abonnement IN ('Domestique', 'Professionnel'));

CREATE INDEX idx_abonnes_ville ON abonnes(ville);
CREATE INDEX idx_abonnes_type ON abonnes(type_abonnement);

CREATE TABLE factures (
    id SERIAL PRIMARY KEY,
    abonne_id INTEGER NOT NULL,
    consommation DECIMAL(10,2) NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    date_emission TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'Emise',
    FOREIGN KEY (abonne_id) REFERENCES abonnes(id) ON DELETE CASCADE
);

ALTER TABLE factures ADD CONSTRAINT check_consommation CHECK (consommation > 0);
ALTER TABLE factures ADD CONSTRAINT check_montant CHECK (montant_total > 0);
ALTER TABLE factures ADD CONSTRAINT check_statut CHECK (statut IN ('Emise', 'Payee'));

CREATE INDEX idx_factures_abonne ON factures(abonne_id);
CREATE INDEX idx_factures_statut ON factures(statut);

CREATE TABLE reclamations (
    id SERIAL PRIMARY KEY,
    facture_id INTEGER NOT NULL,
    statut VARCHAR(50) DEFAULT 'En attente',
    reponse TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (facture_id) REFERENCES factures(id) ON DELETE CASCADE
);

ALTER TABLE reclamations ADD CONSTRAINT check_statut_recla CHECK (statut IN ('En attente', 'En cours', 'Resolue'));

CREATE INDEX idx_reclamations_facture ON reclamations(facture_id);
CREATE INDEX idx_reclamations_statut ON reclamations(statut);

CREATE TABLE operateurs (
    id SERIAL PRIMARY KEY,
    login VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_operateurs_login ON operateurs(login);
