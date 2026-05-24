
// 1. CRÉATION DE LA BASE DE DONNÉES (dans mongo)


// Utiliser la base de données camwater_logs


        // use camwater_logs;


// 2. CRÉATION DE LA COLLECTION



db.createCollection("activites", {
    validator: {
        $jsonSchema: {
            bsonType: "object",
            required: ["type_action", "operateur_id", "timestamp"],
            properties: {
                type_action: {
                    bsonType: "string",
                    description: "Type d'action réalisée dans le système"
                },
                operateur_id: {
                    bsonType: "int",
                    description: "Identifiant de l'opérateur qui a effectué l'action"
                },
                abonne_id: {
                    bsonType: ["int", "null"],
                    description: "Identifiant de l'abonné concerné (si applicable)"
                },
                timestamp: {
                    bsonType: "date",
                    description: "Date et heure de l'action"
                },
                details: {
                    bsonType: "object",
                    description: "Informations spécifiques à l'action"
                }
            }
        }
    }
});


// 3. INSERTION DE LOGS D'EXEMPLE


// Log 1 : Connexion d'un opérateur
db.activites.insertOne({
    type_action: "connexion_operateur",
    operateur_id: 1,
    abonne_id: null,
    timestamp: new Date("2026-03-08T08:30:00Z"),
    details: {
        login: "admin@camwater.cm",
        ip_address: "192.168.1.100",
        user_agent: "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
    }
});

// Log 2 : Génération d'une facture
db.activites.insertOne({
    type_action: "generation_facture",
    operateur_id: 2,
    abonne_id: 10,
    timestamp: new Date("2026-03-08T10:30:00Z"),
    details: {
        consommation: 15,
        montant: 7500,
        type_abonnement: "Domestique",
        numero_compteur: "CPT-YAO-010"
    }
});

// Log 3 : Modification des informations d'un abonné
db.activites.insertOne({
    type_action: "modification_abonne",
    operateur_id: 2,
    abonne_id: 5,
    timestamp: new Date("2026-03-08T11:45:00Z"),
    details: {
        champs_modifies: ["quartier", "ville"],
        anciennes_valeurs: {
            quartier: "Bastos",
            ville: "Yaoundé"
        },
        nouvelles_valeurs: {
            quartier: "Mvan",
            ville: "Yaoundé"
        }
    }
});

// Log 4 : Création d'un nouvel abonné
db.activites.insertOne({
    type_action: "creation_abonne",
    operateur_id: 1,
    abonne_id: 15,
    timestamp: new Date("2026-03-07T14:20:00Z"),
    details: {
        nom: "Nkolo",
        prenom: "Pierre",
        ville: "Douala",
        quartier: "Bonaberi",
        numero_compteur: "CPT-DLA-015",
        type_abonnement: "Domestique"
    }
});

// Log 5 : Traitement d'une réclamation
db.activites.insertOne({
    type_action: "traitement_reclamation",
    operateur_id: 3,
    abonne_id: 8,
    timestamp: new Date("2026-03-06T16:00:00Z"),
    details: {
        reclamation_id: 3,
        facture_id: 12,
        ancien_statut: "En attente",
        nouveau_statut: "Resolue",
        reponse: "Erreur de relevé corrigée. Nouvelle facture émise."
    }
});

// Log 6 : Paiement d'une facture
db.activites.insertOne({
    type_action: "paiement_facture",
    operateur_id: 2,
    abonne_id: 10,
    timestamp: new Date("2026-03-05T09:15:00Z"),
    details: {
        facture_id: 25,
        montant: 12500,
        mode_paiement: "Espèces",
        reference_paiement: "PAY-2026-03-05-001"
    }
});

// Log 7 : Suppression d'un abonné
db.activites.insertOne({
    type_action: "suppression_abonne",
    operateur_id: 1,
    abonne_id: 20,
    timestamp: new Date("2026-03-02T13:30:00Z"),
    details: {
        nom_complet: "Fotso Claire",
        numero_compteur: "CPT-BAF-020",
        raison: "Déménagement définitif"
    }
});

// Log 8 : Consultation de statistiques
db.activites.insertOne({
    type_action: "consultation_statistiques",
    operateur_id: 1,
    abonne_id: null,
    timestamp: new Date("2026-03-08T15:00:00Z"),
    details: {
        type_statistique: "factures_impayees",
        ville: "Yaoundé",
        periode: "Mars 2026"
    }
});


// 4. REQUÊTE : RÉCUPÉRER LES LOGS D'UN OPÉRATEUR SUR LES 7 DERNIERS JOURS


// Calculer la date d'il y a 7 jours
var dateLimite = new Date();
dateLimite.setDate(dateLimite.getDate() - 7);

// Requête pour récupérer les activités d'un opérateur spécifique (exemple : operateur_id = 2)
db.activites.find({
    operateur_id: 2,
    timestamp: {
        $gte: dateLimite
    }
}).sort({
    timestamp: -1
});

