<?php

namespace Tests\Unit;

use App\Models\Facture;
use PHPUnit\Framework\TestCase;

/**
 * Tests unitaires pour la logique de calcul des factures.
 */
class FactureCalculTest extends TestCase
{
    /**
     * Test du calcul pour un abonné domestique avec consommation de 0-10 m³.
     */
    public function test_calcul_domestique_tranche_1(): void
    {
        // Consommation de 8.5 m³
        $montant = Facture::calculerMontant(8.5, 'Domestique');

        // 8.5 * 350 = 2975 FCFA
        $this->assertEquals(2975, $montant);
    }

    /**
     * Test du calcul pour un abonné domestique avec consommation de 11-20 m³.
     */
    public function test_calcul_domestique_tranche_2(): void
    {
        // Consommation de 15 m³
        $montant = Facture::calculerMontant(15, 'Domestique');

        // (10 * 350) + (5 * 550) = 3500 + 2750 = 6250 FCFA
        $this->assertEquals(6250, $montant);
    }

    /**
     * Test du calcul pour un abonné domestique avec consommation > 20 m³.
     */
    public function test_calcul_domestique_tranche_3(): void
    {
        // Consommation de 25 m³
        $montant = Facture::calculerMontant(25, 'Domestique');

        // (10 * 350) + (10 * 550) + (5 * 780) = 3500 + 5500 + 3900 = 12900 FCFA
        $this->assertEquals(12900, $montant);
    }

    /**
     * Test du calcul pour un abonné professionnel.
     */
    public function test_calcul_professionnel(): void
    {
        // Consommation de 25 m³
        $montant = Facture::calculerMontant(25, 'Professionnel');

        // 8500 + (25 * 950) = 8500 + 23750 = 32250 FCFA
        $this->assertEquals(32250, $montant);
    }

    /**
     * Test du calcul pour un abonné professionnel avec faible consommation.
     */
    public function test_calcul_professionnel_faible_consommation(): void
    {
        // Consommation de 5 m³
        $montant = Facture::calculerMontant(5, 'Professionnel');

        // 8500 + (5 * 950) = 8500 + 4750 = 13250 FCFA
        $this->assertEquals(13250, $montant);
    }

    /**
     * Test que le montant est arrondi au FCFA supérieur.
     */
    public function test_arrondi_au_fcfa_superieur(): void
    {
        // Consommation de 8.3 m³ (domestique)
        $montant = Facture::calculerMontant(8.3, 'Domestique');

        // 8.3 * 350 = 2905, mais ceil() arrondit à 2906
        $this->assertEquals(2906, $montant);

        // Vérifier que c'est bien un entier
        $this->assertIsInt($montant);
    }

    /**
     * Test qu'une exception est levée pour une consommation négative.
     */
    public function test_exception_consommation_negative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La consommation doit être strictement positive.');

        Facture::calculerMontant(-5, 'Domestique');
    }

    /**
     * Test qu'une exception est levée pour une consommation nulle.
     */
    public function test_exception_consommation_nulle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La consommation doit être strictement positive.');

        Facture::calculerMontant(0, 'Domestique');
    }

    /**
     * Test qu'une exception est levée pour un type d'abonnement invalide.
     */
    public function test_exception_type_abonnement_invalide(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Type d\'abonnement invalide');

        Facture::calculerMontant(10, 'Industriel');
    }

    /**
     * Test du calcul à la limite entre deux tranches (domestique).
     */
    public function test_calcul_limite_tranche_domestique(): void
    {
        // Exactement 10 m³ (limite tranche 1)
        $montant10 = Facture::calculerMontant(10, 'Domestique');
        $this->assertEquals(3500, $montant10); // 10 * 350

        // Exactement 20 m³ (limite tranche 2)
        $montant20 = Facture::calculerMontant(20, 'Domestique');
        $this->assertEquals(9000, $montant20); // (10 * 350) + (10 * 550)
    }

    /**
     * Test avec des valeurs décimales précises.
     */
    public function test_calcul_avec_decimales(): void
    {
        // Consommation de 12.75 m³ (domestique)
        $montant = Facture::calculerMontant(12.75, 'Domestique');

        // (10 * 350) + (2.75 * 550) = 3500 + 1512.5 = 5012.5 → arrondi à 5013
        $this->assertEquals(5013, $montant);
    }
}
