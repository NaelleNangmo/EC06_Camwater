<?php

namespace Tests\Feature;

use App\Models\Abonne;
use App\Models\Facture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests pour les endpoints de l'API Abonnés.
 * Teste toutes les opérations CRUD sur les abonnés.
 */
class AbonneApiTest extends TestCase
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
     * Test : Récupérer la liste de tous les abonnés.
     * GET /api/abonnes
     */
    public function test_can_get_all_abonnes(): void
    {
        // Créer des abonnés de test
        Abonne::factory()->count(3)->create();

        $response = $this->getJson('/api/abonnes');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'nom',
                             'prenom',
                             'ville',
                             'quartier',
                             'numero_compteur',
                             'type_abonnement',
                             'date_creation',
                             'factures'
                         ]
                     ],
                     'pagination',
                     'message'
                 ])
                 ->assertJson([
                     'success' => true
                 ]);

        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test : Créer un nouvel abonné avec des données valides.
     * POST /api/abonnes
     */
    public function test_can_create_abonne_with_valid_data(): void
    {
        $token = $this->authenticateUser();

        $abonneData = [
            'nom' => 'Kamga',
            'prenom' => 'Jean',
            'ville' => 'Yaoundé',
            'quartier' => 'Bastos',
            'numero_compteur' => 'CPT-YAO-001',
            'type_abonnement' => 'Domestique'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/abonnes', $abonneData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'nom',
                         'prenom',
                         'ville',
                         'quartier',
                         'numero_compteur',
                         'type_abonnement'
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'nom' => 'Kamga',
                         'prenom' => 'Jean',
                         'ville' => 'Yaoundé'
                     ]
                 ]);

        $this->assertDatabaseHas('abonnes', [
            'nom' => 'Kamga',
            'numero_compteur' => 'CPT-YAO-001'
        ]);
    }

    /**
     * Test : Échec de création avec un numéro de compteur déjà existant.
     * POST /api/abonnes
     */
    public function test_cannot_create_abonne_with_duplicate_numero_compteur(): void
    {
        $token = $this->authenticateUser();

        Abonne::factory()->create([
            'numero_compteur' => 'CPT-YAO-001'
        ]);

        $abonneData = [
            'nom' => 'Nkolo',
            'prenom' => 'Paul',
            'ville' => 'Douala',
            'quartier' => 'Akwa',
            'numero_compteur' => 'CPT-YAO-001', // Déjà existant
            'type_abonnement' => 'Domestique'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/abonnes', $abonneData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['numero_compteur']);
    }

    /**
     * Test : Échec de création avec des données manquantes.
     * POST /api/abonnes
     */
    public function test_cannot_create_abonne_with_missing_fields(): void
    {
        $token = $this->authenticateUser();

        $abonneData = [
            'nom' => 'Kamga',
            // Champs manquants : prenom, ville, quartier, numero_compteur, type_abonnement
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/abonnes', $abonneData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'prenom',
                     'ville',
                     'quartier',
                     'numero_compteur',
                     'type_abonnement'
                 ]);
    }

    /**
     * Test : Échec de création avec une ville invalide.
     * POST /api/abonnes
     */
    public function test_cannot_create_abonne_with_invalid_ville(): void
    {
        $token = $this->authenticateUser();

        $abonneData = [
            'nom' => 'Kamga',
            'prenom' => 'Jean',
            'ville' => 'Paris', // Ville non autorisée
            'quartier' => 'Bastos',
            'numero_compteur' => 'CPT-YAO-001',
            'type_abonnement' => 'Domestique'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/abonnes', $abonneData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['ville']);
    }

    /**
     * Test : Récupérer un abonné spécifique avec ses factures.
     * GET /api/abonnes/{id}
     */
    public function test_can_get_single_abonne_with_factures(): void
    {
        $abonne = Abonne::factory()->create();
        Facture::factory()->count(2)->create(['abonne_id' => $abonne->id]);

        $response = $this->getJson("/api/abonnes/{$abonne->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'id',
                         'nom',
                         'prenom',
                         'ville',
                         'factures' => [
                             '*' => [
                                 'id',
                                 'consommation',
                                 'montant_total',
                                 'statut'
                             ]
                         ]
                     ],
                     'message'
                 ])
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $abonne->id
                     ]
                 ]);

        $this->assertCount(2, $response->json('data.factures'));
    }

    /**
     * Test : Échec de récupération d'un abonné inexistant.
     * GET /api/abonnes/{id}
     */
    public function test_cannot_get_nonexistent_abonne(): void
    {
        $response = $this->getJson('/api/abonnes/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false
                 ]);
    }

    /**
     * Test : Mettre à jour un abonné existant.
     * PUT /api/abonnes/{id}
     */
    public function test_can_update_abonne(): void
    {
        $token = $this->authenticateUser();

        $abonne = Abonne::factory()->create([
            'quartier' => 'Bastos',
            'ville' => 'Yaoundé'
        ]);

        $updateData = [
            'quartier' => 'Mvan',
            'ville' => 'Douala'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/abonnes/{$abonne->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $abonne->id,
                         'quartier' => 'Mvan',
                         'ville' => 'Douala'
                     ]
                 ]);

        $this->assertDatabaseHas('abonnes', [
            'id' => $abonne->id,
            'quartier' => 'Mvan',
            'ville' => 'Douala'
        ]);
    }

    /**
     * Test : Supprimer un abonné et ses factures (cascade).
     * DELETE /api/abonnes/{id}
     */
    public function test_can_delete_abonne_and_cascade_factures(): void
    {
        $token = $this->authenticateUser();

        $abonne = Abonne::factory()->create();
        $facture = Facture::factory()->create(['abonne_id' => $abonne->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/abonnes/{$abonne->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ]);

        $this->assertDatabaseMissing('abonnes', ['id' => $abonne->id]);
        $this->assertDatabaseMissing('factures', ['id' => $facture->id]);
    }

    /**
     * Test : Échec de suppression d'un abonné inexistant.
     * DELETE /api/abonnes/{id}
     */
    public function test_cannot_delete_nonexistent_abonne(): void
    {
        $token = $this->authenticateUser();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/abonnes/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false
                 ]);
    }
}
