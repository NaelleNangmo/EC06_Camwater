<?php

namespace Tests\Feature;

use App\Models\Abonne;
use App\Models\Facture;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test de pagination sur la liste des abonnés.
     */
    public function test_pagination_abonnes(): void
    {
        // Créer 20 abonnés
        Abonne::factory()->count(20)->create();

        // Récupérer la première page
        $response = $this->getJson('/api/abonnes?page=1');

        // Vérifier la pagination
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                    'from',
                    'to',
                ],
            ]);

        // Vérifier les valeurs de pagination
        $pagination = $response->json('pagination');
        $this->assertEquals(20, $pagination['total']);
        $this->assertEquals(15, $pagination['per_page']);
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(2, $pagination['last_page']);
    }

    /**
     * Test de pagination sur la liste des factures.
     */
    public function test_pagination_factures(): void
    {
        // Créer un abonné et 20 factures
        $abonne = Abonne::factory()->create();
        Facture::factory()->count(20)->create([
            'abonne_id' => $abonne->id,
        ]);

        // Récupérer la première page
        $response = $this->getJson('/api/factures?page=1');

        // Vérifier la pagination
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ],
            ]);

        // Vérifier les valeurs
        $pagination = $response->json('pagination');
        $this->assertEquals(20, $pagination['total']);
        $this->assertEquals(15, $pagination['per_page']);
    }

    /**
     * Test de navigation entre les pages.
     */
    public function test_navigation_entre_pages(): void
    {
        // Créer 30 abonnés
        Abonne::factory()->count(30)->create();

        // Page 1
        $response1 = $this->getJson('/api/abonnes?page=1');
        $response1->assertStatus(200);
        $this->assertEquals(1, $response1->json('pagination.current_page'));
        $this->assertCount(15, $response1->json('data'));

        // Page 2
        $response2 = $this->getJson('/api/abonnes?page=2');
        $response2->assertStatus(200);
        $this->assertEquals(2, $response2->json('pagination.current_page'));
        $this->assertCount(15, $response2->json('data'));
    }

    /**
     * Test du nombre d'éléments par page.
     */
    public function test_nombre_elements_par_page(): void
    {
        // Créer 10 abonnés
        Abonne::factory()->count(10)->create();

        // Récupérer la liste
        $response = $this->getJson('/api/abonnes');

        // Vérifier qu'il y a 10 éléments (moins que la limite de 15)
        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
        $this->assertEquals(10, $response->json('pagination.total'));
    }
}
