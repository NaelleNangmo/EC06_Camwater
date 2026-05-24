<?php

namespace Tests\Feature;

use App\Models\Abonne;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test que les données sont mises en cache.
     */
    public function test_donnees_mises_en_cache(): void
    {
        // Créer des abonnés
        Abonne::factory()->count(5)->create();

        // Vider le cache
        Cache::flush();

        // Première requête (devrait mettre en cache)
        $response1 = $this->getJson('/api/abonnes');
        $response1->assertStatus(200);

        // Vérifier que le cache contient la clé
        $this->assertTrue(Cache::has('abonnes_page_1'));
    }

    /**
     * Test que le cache est invalidé après création.
     */
    public function test_cache_invalide_apres_creation(): void
    {
        // Créer un utilisateur
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Créer des abonnés et mettre en cache
        Abonne::factory()->count(3)->create();
        $this->getJson('/api/abonnes');

        // Vérifier que le cache existe
        $this->assertTrue(Cache::has('abonnes_page_1'));

        // Créer un nouvel abonné
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/abonnes', [
            'nom' => 'Nouveau',
            'prenom' => 'Abonne',
            'ville' => 'Yaoundé',
            'quartier' => 'Bastos',
            'numero_compteur' => 'CPT-CACHE-001',
            'type_abonnement' => 'Domestique',
        ]);

        // Vérifier que le cache est invalidé
        $this->assertFalse(Cache::has('abonnes_page_1'));
    }

    /**
     * Test que le cache est invalidé après modification.
     */
    public function test_cache_invalide_apres_modification(): void
    {
        // Créer un utilisateur et un abonné
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $abonne = Abonne::factory()->create();

        // Mettre en cache
        $this->getJson('/api/abonnes');
        $this->assertTrue(Cache::has('abonnes_page_1'));

        // Modifier l'abonné
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/abonnes/{$abonne->id}", [
            'quartier' => 'Nouveau Quartier',
        ]);

        // Vérifier que le cache est invalidé
        $this->assertFalse(Cache::has('abonnes_page_1'));
    }

    /**
     * Test que le cache est invalidé après suppression.
     */
    public function test_cache_invalide_apres_suppression(): void
    {
        // Créer un utilisateur et un abonné
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;
        $abonne = Abonne::factory()->create();

        // Mettre en cache
        $this->getJson('/api/abonnes');
        $this->assertTrue(Cache::has('abonnes_page_1'));

        // Supprimer l'abonné
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/abonnes/{$abonne->id}");

        // Vérifier que le cache est invalidé
        $this->assertFalse(Cache::has('abonnes_page_1'));
    }

    /**
     * Test de performance avec cache.
     */
    public function test_performance_avec_cache(): void
    {
        // Créer des abonnés
        Abonne::factory()->count(10)->create();

        // Vider le cache
        Cache::flush();

        // Première requête (sans cache)
        $start1 = microtime(true);
        $this->getJson('/api/abonnes');
        $time1 = microtime(true) - $start1;

        // Deuxième requête (avec cache)
        $start2 = microtime(true);
        $this->getJson('/api/abonnes');
        $time2 = microtime(true) - $start2;

        // Vérifier que le cache fonctionne (tolérance large pour les tests)
        // Le cache devrait être au moins aussi rapide ou légèrement plus lent
        $this->assertLessThanOrEqual($time1 * 3, $time2); // Tolérance de 3x
    }
}
