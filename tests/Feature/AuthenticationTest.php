<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de connexion avec des identifiants valides.
     */
    public function test_login_avec_identifiants_valides(): void
    {
        // Créer un utilisateur
        $user = User::factory()->create([
            'email' => 'test@camwater.cm',
            'password' => Hash::make('password123'),
        ]);

        // Tenter de se connecter
        $response = $this->postJson('/api/login', [
            'email' => 'test@camwater.cm',
            'password' => 'password123',
        ]);

        // Vérifier la réponse
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                    'token_type',
                ],
            ])
            ->assertJson([
                'success' => true,
            ]);

        // Vérifier que le token est présent
        $this->assertNotEmpty($response->json('data.token'));
    }

    /**
     * Test de connexion avec des identifiants invalides.
     */
    public function test_login_avec_identifiants_invalides(): void
    {
        // Créer un utilisateur
        User::factory()->create([
            'email' => 'test@camwater.cm',
            'password' => Hash::make('password123'),
        ]);

        // Tenter de se connecter avec un mauvais mot de passe
        $response = $this->postJson('/api/login', [
            'email' => 'test@camwater.cm',
            'password' => 'mauvais_password',
        ]);

        // Vérifier que la connexion échoue
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test de génération du token.
     */
    public function test_generation_du_token(): void
    {
        // Créer un utilisateur
        $user = User::factory()->create([
            'email' => 'test@camwater.cm',
            'password' => Hash::make('password123'),
        ]);

        // Se connecter
        $response = $this->postJson('/api/login', [
            'email' => 'test@camwater.cm',
            'password' => 'password123',
        ]);

        // Vérifier que le token est généré
        $token = $response->json('data.token');
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }

    /**
     * Test d'accès à une route protégée avec token.
     */
    public function test_acces_route_protegee_avec_token(): void
    {
        // Créer un utilisateur
        $user = User::factory()->create();

        // Générer un token
        $token = $user->createToken('test-token')->plainTextToken;

        // Accéder à une route protégée
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson('/api/me');

        // Vérifier l'accès
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    /**
     * Test d'accès refusé sans token.
     */
    public function test_acces_refuse_sans_token(): void
    {
        // Tenter d'accéder à une route protégée sans token
        $response = $this->postJson('/api/abonnes', [
            'nom' => 'Test',
            'prenom' => 'User',
            'ville' => 'Yaoundé',
            'quartier' => 'Bastos',
            'numero_compteur' => 'CPT-TEST-001',
            'type_abonnement' => 'Domestique',
        ]);

        // Vérifier que l'accès est refusé
        $response->assertStatus(401);
    }

    /**
     * Test de déconnexion.
     */
    public function test_logout(): void
    {
        // Créer un utilisateur
        $user = User::factory()->create();

        // Générer un token
        $token = $user->createToken('test-token')->plainTextToken;

        // Se déconnecter
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson('/api/logout');

        // Vérifier la déconnexion
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Déconnexion réussie.',
            ]);

        // Vérifier que le token est révoqué
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }
}
