<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Abonne;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecuredRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test d'accès autorisé avec token pour créer un abonné.
     */
    public function test_creation_abonne_avec_token(): void
    {
        // Créer un utilisateur et générer un token
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Créer un abonné
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/abonnes', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'ville' => 'Yaoundé',
            'quartier' => 'Bastos',
            'numero_compteur' => 'CPT-TEST-001',
            'type_abonnement' => 'Domestique',
        ]);

        // Vérifier la création
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Abonné créé avec succès.',
            ]);
    }

    /**
     * Test d'accès refusé sans authentification pour créer un abonné.
     */
    public function test_creation_abonne_sans_token(): void
    {
        // Tenter de créer un abonné sans token
        $response = $this->postJson('/api/abonnes', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'ville' => 'Yaoundé',
            'quartier' => 'Bastos',
            'numero_compteur' => 'CPT-TEST-002',
            'type_abonnement' => 'Domestique',
        ]);

        // Vérifier que l'accès est refusé
        $response->assertStatus(401);
    }

    /**
     * Test d'accès autorisé avec token pour modifier un abonné.
     */
    public function test_modification_abonne_avec_token(): void
    {
        // Créer un utilisateur et un abonné
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $abonne = Abonne::factory()->create();

        // Modifier l'abonné
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/abonnes/{$abonne->id}", [
            'quartier' => 'Mvan',
        ]);

        // Vérifier la modification
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Abonné mis à jour avec succès.',
            ]);
    }

    /**
     * Test d'accès refusé sans authentification pour modifier un abonné.
     */
    public function test_modification_abonne_sans_token(): void
    {
        // Créer un abonné
        $abonne = Abonne::factory()->create();

        // Tenter de modifier sans token
        $response = $this->putJson("/api/abonnes/{$abonne->id}", [
            'quartier' => 'Mvan',
        ]);

        // Vérifier que l'accès est refusé
        $response->assertStatus(401);
    }

    /**
     * Test d'accès autorisé avec token pour supprimer un abonné.
     */
    public function test_suppression_abonne_avec_token(): void
    {
        // Créer un utilisateur et un abonné
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        
        $abonne = Abonne::factory()->create();

        // Supprimer l'abonné
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/abonnes/{$abonne->id}");

        // Vérifier la suppression
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Abonné supprimé avec succès.',
            ]);
    }

    /**
     * Test d'accès refusé sans authentification pour supprimer un abonné.
     */
    public function test_suppression_abonne_sans_token(): void
    {
        // Créer un abonné
        $abonne = Abonne::factory()->create();

        // Tenter de supprimer sans token
        $response = $this->deleteJson("/api/abonnes/{$abonne->id}");

        // Vérifier que l'accès est refusé
        $response->assertStatus(401);
    }

    /**
     * Test d'accès public pour consulter les abonnés.
     */
    public function test_consultation_abonnes_sans_token(): void
    {
        // Créer quelques abonnés
        Abonne::factory()->count(3)->create();

        // Consulter la liste sans token
        $response = $this->getJson('/api/abonnes');

        // Vérifier l'accès
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
