<?php

namespace Tests\Feature;

use App\Models\Abonne;
use App\Models\Facture;
use App\Models\Reclamation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests pour les endpoints de l'API Factures.
 * Teste toutes les opérations sur les factures incluant la génération automatique.
 */
class FactureApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Créer un utilisateur authentifié et retourner le token.
     */
    protected function authenticateUser(): string
    {
        $user = User::factory()->create();

        return $user->createToken('test-token')->plainTextToken;
    }

    /**
     * Test : Récupérer la liste de toutes les factures avec les abonnés.
     * GET /api/factures
     */
    public function test_can_get_all_factures(): void
    {
        $abonne = Abonne::factory()->create();
        Facture::factory()->count(3)->create(['abonne_id' => $abonne->id]);

        $response = $this->getJson('/api/factures');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination',
                'message',
            ])
            ->assertJson([
                'success' => true,
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test : Générer une facture pour un abonné domestique.
     * POST /api/factures/generer
     */
    public function test_can_generate_facture_for_domestique_abonne(): void
    {
        $token = $this->authenticateUser();

        $abonne = Abonne::factory()->create([
            'type_abonnement' => 'Domestique',
        ]);

        $factureData = [
            'abonne_id' => $abonne->id,
            'consommation' => 15.5,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', $factureData);

        // Calcul attendu : (10 * 350) + (5.5 * 550) = 3500 + 3025 = 6525 FCFA
        $montantAttendu = 6525;

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'abonne_id' => $abonne->id,
                    'consommation' => '15.50',
                    'montant_total' => (string) $montantAttendu,
                    'statut' => 'Emise',
                ],
            ]);

        $this->assertDatabaseHas('factures', [
            'abonne_id' => $abonne->id,
            'consommation' => 15.5,
            'montant_total' => $montantAttendu,
        ]);
    }

    /**
     * Test : Générer une facture pour un abonné professionnel.
     * POST /api/factures/generer
     */
    public function test_can_generate_facture_for_professionnel_abonne(): void
    {
        $token = $this->authenticateUser();

        $abonne = Abonne::factory()->create([
            'type_abonnement' => 'Professionnel',
        ]);

        $factureData = [
            'abonne_id' => $abonne->id,
            'consommation' => 25,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', $factureData);

        // Calcul attendu : 8500 + (25 * 950) = 8500 + 23750 = 32250 FCFA
        $montantAttendu = 32250;

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'abonne_id' => $abonne->id,
                    'consommation' => '25.00',
                    'montant_total' => (string) $montantAttendu,
                    'statut' => 'Emise',
                ],
            ]);

        $this->assertDatabaseHas('factures', [
            'abonne_id' => $abonne->id,
            'montant_total' => $montantAttendu,
        ]);
    }

    /**
     * Test : Calcul correct pour abonné domestique tranche 1 (0-10 m³).
     */
    public function test_calculates_correctly_for_domestique_tranche_1(): void
    {
        $token = $this->authenticateUser();
        $abonne = Abonne::factory()->create(['type_abonnement' => 'Domestique']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => $abonne->id,
            'consommation' => 8,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'montant_total' => '2800',
                ],
            ]);
    }

    /**
     * Test : Calcul correct pour abonné domestique tranche 2 (11-20 m³).
     */
    public function test_calculates_correctly_for_domestique_tranche_2(): void
    {
        $token = $this->authenticateUser();
        $abonne = Abonne::factory()->create(['type_abonnement' => 'Domestique']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => $abonne->id,
            'consommation' => 15,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'montant_total' => '6250',
                ],
            ]);
    }

    /**
     * Test : Calcul correct pour abonné domestique tranche 3 (> 20 m³).
     */
    public function test_calculates_correctly_for_domestique_tranche_3(): void
    {
        $token = $this->authenticateUser();
        $abonne = Abonne::factory()->create(['type_abonnement' => 'Domestique']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => $abonne->id,
            'consommation' => 25,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'montant_total' => '12900',
                ],
            ]);
    }

    /**
     * Test : Échec de génération avec consommation négative.
     */
    public function test_cannot_generate_facture_with_negative_consommation(): void
    {
        $token = $this->authenticateUser();
        $abonne = Abonne::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => $abonne->id,
            'consommation' => -5,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['consommation']);
    }

    /**
     * Test : Échec de génération avec consommation zéro.
     */
    public function test_cannot_generate_facture_with_zero_consommation(): void
    {
        $token = $this->authenticateUser();
        $abonne = Abonne::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => $abonne->id,
            'consommation' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['consommation']);
    }

    /**
     * Test : Échec de génération pour un abonné inexistant.
     */
    public function test_cannot_generate_facture_for_nonexistent_abonne(): void
    {
        $token = $this->authenticateUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => 999,
            'consommation' => 15,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['abonne_id']);
    }

    /**
     * Test : Récupérer une facture spécifique avec abonné et réclamations.
     */
    public function test_can_get_single_facture_with_relations(): void
    {
        $abonne = Abonne::factory()->create();
        $facture = Facture::factory()->create(['abonne_id' => $abonne->id]);
        Reclamation::factory()->count(2)->create(['facture_id' => $facture->id]);

        $response = $this->getJson("/api/factures/{$facture->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $facture->id,
                ],
            ]);

        $this->assertCount(2, $response->json('data.reclamations'));
    }

    /**
     * Test : Échec de récupération d'une facture inexistante.
     */
    public function test_cannot_get_nonexistent_facture(): void
    {
        $response = $this->getJson('/api/factures/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test : Mettre à jour le statut d'une facture (Emise → Payee).
     */
    public function test_can_update_facture_statut_to_payee(): void
    {
        $token = $this->authenticateUser();
        $facture = Facture::factory()->create(['statut' => 'Emise']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->putJson("/api/factures/{$facture->id}", [
            'statut' => 'Payee',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $facture->id,
                    'statut' => 'Payee',
                ],
            ]);

        $this->assertDatabaseHas('factures', [
            'id' => $facture->id,
            'statut' => 'Payee',
        ]);
    }

    /**
     * Test : Échec de mise à jour avec un statut invalide.
     */
    public function test_cannot_update_facture_with_invalid_statut(): void
    {
        $token = $this->authenticateUser();
        $facture = Facture::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->putJson("/api/factures/{$facture->id}", [
            'statut' => 'InvalidStatus',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['statut']);
    }

    /**
     * Test : Supprimer une facture.
     */
    public function test_can_delete_facture(): void
    {
        $token = $this->authenticateUser();
        $facture = Facture::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson("/api/factures/{$facture->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('factures', ['id' => $facture->id]);
    }

    /**
     * Test : Échec de suppression d'une facture inexistante.
     */
    public function test_cannot_delete_nonexistent_facture(): void
    {
        $token = $this->authenticateUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->deleteJson('/api/factures/999');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test : Génération de plusieurs factures pour le même abonné.
     */
    public function test_can_generate_multiple_factures_for_same_abonne(): void
    {
        $token = $this->authenticateUser();
        $abonne = Abonne::factory()->create(['type_abonnement' => 'Domestique']);

        // Première facture
        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => $abonne->id,
            'consommation' => 10,
        ])->assertStatus(201);

        // Deuxième facture
        $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/factures/generer', [
            'abonne_id' => $abonne->id,
            'consommation' => 15,
        ])->assertStatus(201);

        $this->assertDatabaseCount('factures', 2);
    }
}
