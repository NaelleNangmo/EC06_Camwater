<?php

namespace Database\Seeders;

use App\Models\Abonne;
use App\Models\Facture;
use App\Models\Reclamation;
use App\Models\Operateur;
use Illuminate\Database\Seeder;

/**
 * Seeder principal pour peupler la base de données avec des données de test.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Exécute le seeding de la base de données.
     */
    public function run(): void
    {
        // Créer des opérateurs
        $this->seedOperateurs();
        
        // Créer des abonnés
        $this->seedAbonnes();
        
        // Créer des factures
        $this->seedFactures();
        
        // Créer des réclamations
        $this->seedReclamations();
    }

    /**
     * Crée des opérateurs de test.
     */
    private function seedOperateurs(): void
    {
        Operateur::create([
            'login' => 'admin@camwater.cm',
            'password' => 'admin123', // Sera automatiquement haché
            'role' => 'Administrateur',
        ]);

        Operateur::create([
            'login' => 'operateur1@camwater.cm',
            'password' => 'operateur123',
            'role' => 'Opérateur',
        ]);

        Operateur::create([
            'login' => 'operateur2@camwater.cm',
            'password' => 'operateur123',
            'role' => 'Opérateur',
        ]);
    }

    /**
     * Crée des abonnés de test (5 abonnés dont 2 professionnels).
     */
    private function seedAbonnes(): void
    {
        // Abonné 1 : Domestique à Yaoundé
        Abonne::create([
            'nom' => 'Kamga',
            'prenom' => 'Jean',
            'ville' => 'Yaoundé',
            'quartier' => 'Bastos',
            'numero_compteur' => 'CPT-YAO-001',
            'type_abonnement' => 'Domestique',
        ]);

        // Abonné 2 : Professionnel à Douala
        Abonne::create([
            'nom' => 'Mballa',
            'prenom' => 'Marie',
            'ville' => 'Douala',
            'quartier' => 'Akwa',
            'numero_compteur' => 'CPT-DLA-002',
            'type_abonnement' => 'Professionnel',
        ]);

        // Abonné 3 : Domestique à Yaoundé
        Abonne::create([
            'nom' => 'Nkolo',
            'prenom' => 'Paul',
            'ville' => 'Yaoundé',
            'quartier' => 'Mvan',
            'numero_compteur' => 'CPT-YAO-003',
            'type_abonnement' => 'Domestique',
        ]);

        // Abonné 4 : Professionnel à Bafoussam
        Abonne::create([
            'nom' => 'Fotso',
            'prenom' => 'Claire',
            'ville' => 'Bafoussam',
            'quartier' => 'Centre-ville',
            'numero_compteur' => 'CPT-BAF-004',
            'type_abonnement' => 'Professionnel',
        ]);

        // Abonné 5 : Domestique à Garoua
        Abonne::create([
            'nom' => 'Bello',
            'prenom' => 'Amadou',
            'ville' => 'Garoua',
            'quartier' => 'Plateau',
            'numero_compteur' => 'CPT-GAR-005',
            'type_abonnement' => 'Domestique',
        ]);
    }

    /**
     * Crée des factures de test.
     */
    private function seedFactures(): void
    {
        // Facture 1 : Abonné 1 (Domestique) - 8.5 m³
        $montant1 = Facture::calculerMontant(8.5, 'Domestique');
        Facture::create([
            'abonne_id' => 1,
            'consommation' => 8.5,
            'montant_total' => $montant1,
            'date_emission' => now()->subDays(20),
            'statut' => 'Payee',
        ]);

        // Facture 2 : Abonné 2 (Professionnel) - 25 m³
        $montant2 = Facture::calculerMontant(25, 'Professionnel');
        Facture::create([
            'abonne_id' => 2,
            'consommation' => 25,
            'montant_total' => $montant2,
            'date_emission' => now()->subDays(7),
            'statut' => 'Emise',
        ]);

        // Facture 3 : Abonné 3 (Domestique) - 15 m³
        $montant3 = Facture::calculerMontant(15, 'Domestique');
        Facture::create([
            'abonne_id' => 3,
            'consommation' => 15,
            'montant_total' => $montant3,
            'date_emission' => now()->subDays(3),
            'statut' => 'Emise',
        ]);

        // Facture 4 : Abonné 4 (Professionnel) - 30 m³
        $montant4 = Facture::calculerMontant(30, 'Professionnel');
        Facture::create([
            'abonne_id' => 4,
            'consommation' => 30,
            'montant_total' => $montant4,
            'date_emission' => now()->subDays(10),
            'statut' => 'Payee',
        ]);

        // Facture 5 : Abonné 5 (Domestique) - 22 m³
        $montant5 = Facture::calculerMontant(22, 'Domestique');
        Facture::create([
            'abonne_id' => 5,
            'consommation' => 22,
            'montant_total' => $montant5,
            'date_emission' => now()->subDays(5),
            'statut' => 'Emise',
        ]);
    }

    /**
     * Crée des réclamations de test.
     */
    private function seedReclamations(): void
    {
        // Réclamation 1 : Sur la facture 2
        Reclamation::create([
            'facture_id' => 2,
            'statut' => 'En attente',
            'reponse' => null,
            'date_creation' => now()->subDays(5),
        ]);

        // Réclamation 2 : Sur la facture 3
        Reclamation::create([
            'facture_id' => 3,
            'statut' => 'En cours',
            'reponse' => 'Vérification en cours du relevé du compteur.',
            'date_creation' => now()->subDays(2),
        ]);

        // Réclamation 3 : Sur la facture 5
        Reclamation::create([
            'facture_id' => 5,
            'statut' => 'Resolue',
            'reponse' => 'Erreur de relevé corrigée. Nouvelle facture émise avec le montant correct.',
            'date_creation' => now()->subDays(4),
        ]);
    }
}
