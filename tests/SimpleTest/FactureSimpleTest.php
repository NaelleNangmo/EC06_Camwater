<?php

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/../../vendor/simpletest/simpletest/src/autorun.php';

$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Facture;

/**
 * Tests SimpleTest pour Facture::calculerMontant().
 */
class FactureSimpleTest extends UnitTestCase
{
    // ── Domestique ────────────────────────────────────────────────────────────

    public function test_domestique_tranche_un()
    {
        $result = Facture::calculerMontant(8, 'Domestique');
        $this->assertEqual(2800, $result, 'Tranche 1 : 8m³ × 350 = 2800');
    }

    public function test_domestique_limite_tranche_un()
    {
        $result = Facture::calculerMontant(10, 'Domestique');
        $this->assertEqual(3500, $result, 'Tranche 1 limite : 10m³ × 350 = 3500');
    }

    public function test_domestique_tranche_deux()
    {
        $result = Facture::calculerMontant(15, 'Domestique');
        $this->assertEqual(6250, $result, 'Tranche 2 : (10×350)+(5×550) = 6250');
    }

    public function test_domestique_tranche_trois()
    {
        $result = Facture::calculerMontant(25, 'Domestique');
        $this->assertEqual(12900, $result, 'Tranche 3 : (10×350)+(10×550)+(5×780) = 12900');
    }

    // ── Professionnel ─────────────────────────────────────────────────────────

    public function test_professionnel()
    {
        $result = Facture::calculerMontant(25, 'Professionnel');
        $this->assertEqual(32250, $result, 'Pro : 8500+(25×950) = 32250');
    }

    public function test_professionnel_petite_consommation()
    {
        $result = Facture::calculerMontant(1, 'Professionnel');
        $this->assertEqual(9450, $result, 'Pro : 8500+(1×950) = 9450');
    }

    // ── Cas d'erreur ──────────────────────────────────────────────────────────

    public function test_consommation_negative_lance_exception()
    {
        try {
            Facture::calculerMontant(-5, 'Domestique');
            $this->fail('Une exception était attendue pour consommation négative');
        } catch (\InvalidArgumentException $e) {
            $this->pass();
        }
    }

    public function test_consommation_zero_lance_exception()
    {
        try {
            Facture::calculerMontant(0, 'Domestique');
            $this->fail('Une exception était attendue pour consommation zéro');
        } catch (\InvalidArgumentException $e) {
            $this->pass();
        }
    }

    public function test_type_invalide_lance_exception()
    {
        try {
            Facture::calculerMontant(10, 'Inconnu');
            $this->fail('Une exception était attendue pour type invalide');
        } catch (\InvalidArgumentException $e) {
            $this->pass();
        }
    }
}
