-- 1. INSERTION DE DONNÉES DE TEST


-- Insertion de 5 abonnés (dont 2 professionnels et 2 villes différentes)
INSERT INTO abonnes (nom, prenom, ville, quartier, numero_compteur, type_abonnement, date_creation)
VALUES
    ('Kamga', 'Jean', 'Yaoundé', 'Bastos', 'CPT-YAO-001', 'Domestique', CURRENT_TIMESTAMP),
    ('Mballa', 'Marie', 'Douala', 'Akwa', 'CPT-DLA-002', 'Professionnel', CURRENT_TIMESTAMP),
    ('Nkolo', 'Paul', 'Yaoundé', 'Mvan', 'CPT-YAO-003', 'Domestique', CURRENT_TIMESTAMP),
    ('Fotso', 'Claire', 'Bafoussam', 'Centre-ville', 'CPT-BAF-004', 'Professionnel', CURRENT_TIMESTAMP),
    ('Bello', 'Amadou', 'Garoua', 'Plateau', 'CPT-GAR-005', 'Domestique', CURRENT_TIMESTAMP);

-- Insertion de 3 factures
INSERT INTO factures (abonne_id, consommation, montant_total, date_emission, statut)
VALUES
    (1, 8.5, 2975, '2026-02-15 10:30:00', 'Payee'),
    (2, 25.0, 32250, '2026-03-01 14:20:00', 'Emise'),
    (3, 15.0, 6250, '2026-03-05 09:15:00', 'Emise');


-- 2. REQUÊTE SELECT AVEC JOINTURE
-- Afficher : nom complet, ville, consommation, montant
-- Trié par date décroissante


SELECT
    CONCAT(a.prenom, ' ', a.nom) AS nom_complet,
    a.ville,
    f.consommation,
    f.montant_total AS montant,
    f.date_emission
FROM
    factures f
INNER JOIN
    abonnes a ON f.abonne_id = a.id
ORDER BY
    f.date_emission DESC;


-- 3. REQUÊTE SELECT AVEC AGRÉGATION
-- Total des factures par ville pour le mois courant



SELECT
    a.ville,
    COUNT(f.id) AS nombre_factures,
    SUM(f.montant_total) AS total_montant,
    AVG(f.consommation) AS consommation_moyenne
FROM
    factures f
INNER JOIN
    abonnes a ON f.abonne_id = a.id
WHERE
    EXTRACT(YEAR FROM f.date_emission) = EXTRACT(YEAR FROM CURRENT_DATE)
    AND EXTRACT(MONTH FROM f.date_emission) = EXTRACT(MONTH FROM CURRENT_DATE)
GROUP BY
    a.ville
ORDER BY
    total_montant DESC;


-- 4. REQUÊTE UPDATE
-- Changer le statut d'une facture de 'Emise' à 'Payee'


-- Exemple : Mettre à jour la facture avec l'ID 2
UPDATE factures
SET
    statut = 'Payee',
    updated_at = CURRENT_TIMESTAMP
WHERE
    id = 2
    AND statut = 'Emise';

-- Mettre à jour toutes les factures émises d'un abonné spécifique
UPDATE factures
SET
    statut = 'Payee',
    updated_at = CURRENT_TIMESTAMP
WHERE
    abonne_id = 3
    AND statut = 'Emise';


-- 5. REQUÊTE DELETE
-- Supprimer les réclamations résolues datant de plus de 6 mois


DELETE FROM reclamations
WHERE
    statut = 'Resolue'
    AND date_creation < CURRENT_DATE - INTERVAL '6 months';


-- 6. CRÉATION D'UNE VUE
-- Vue des factures impayées avec informations complètes


CREATE OR REPLACE VIEW vue_factures_impayees AS
SELECT
    f.id AS facture_id,
    f.date_emission,
    f.consommation,
    f.montant_total,
    a.id AS abonne_id,
    CONCAT(a.prenom, ' ', a.nom) AS nom_complet,
    a.ville,
    a.quartier,
    a.numero_compteur,
    a.type_abonnement,
    -- Calcul du nombre de jours de retard
    CURRENT_DATE - DATE(f.date_emission) AS jours_retard
FROM
    factures f
INNER JOIN
    abonnes a ON f.abonne_id = a.id
WHERE
    f.statut = 'Emise'
ORDER BY
    f.date_emission ASC;

-- Utilisation de la vue
SELECT * FROM vue_factures_impayees;


-- REQUÊTES POUR LA CREATION DE L'UTILISATEUR camwater_app


CREATE USER 'camwater_app'@'%' IDENTIFIED VIA camwater USING '***';
GRANT SELECT, INSERT, UPDATE, DELETE ON `camwater\_db`.* TO 'camwater_app'@'%'
REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
