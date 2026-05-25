<?php
namespace Tests\Unit;
use App\Services\LivraisonService;
use PHPUnit\Framework\TestCase;
// Tests unitaires du service de suivi livraison
class LivraisonServiceTest extends TestCase
{
    private LivraisonService $service;
    protected function setUp(): void { parent::setUp(); $this->service = new LivraisonService(); }

    public function test_calcul_distance_retourne_float(): void
    {
        $distance = $this->service->calculerDistance("Douala Centre", "Yaounde Bastos");
        $this->assertIsFloat($distance);
        $this->assertGreaterThanOrEqual(0, $distance);
    }
    public function test_calcul_distance_meme_adresse_retourne_zero(): void
    {
        $distance = $this->service->calculerDistance("Douala", "Douala");
        $this->assertEquals(0.0, $distance);
    }
    public function test_calcul_distance_symetrique(): void
    {
        $d1 = $this->service->calculerDistance("A", "B");
        $d2 = $this->service->calculerDistance("B", "A");
        $this->assertEquals($d1, $d2);
    }
}
