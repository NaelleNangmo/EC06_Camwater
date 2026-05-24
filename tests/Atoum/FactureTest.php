<?php

namespace Tests\Atoum\App\Models;

use atoum\atoum;

/**
 * Tests atoum pour App\Models\Facture::calculerMontant().
 * Logique de tarification eau CAMWATER — aucune base de données requise.
 *
 * @namespace Tests\Atoum
 */
class Facture extends atoum\test
{
    // ── Domestique tranche 1 : 0–10 m³ à 350 FCFA ──────────────────────────

    public function testDomestiqueTrancheUn()
    {
        $this->integer(\App\Models\Facture::calculerMontant(8, 'Domestique'))
             ->isEqualTo(2800); // 8 × 350
    }

    public function testDomestiqueLimiteTrancheUn()
    {
        $this->integer(\App\Models\Facture::calculerMontant(10, 'Domestique'))
             ->isEqualTo(3500); // 10 × 350
    }

    // ── Domestique tranche 2 : 11–20 m³ à 550 FCFA ──────────────────────────

    public function testDomestiqueTrancheDeux()
    {
        $this->integer(\App\Models\Facture::calculerMontant(15, 'Domestique'))
             ->isEqualTo(6250); // (10×350) + (5×550)
    }

    public function testDomestiqueLimiteTrancheDeux()
    {
        $this->integer(\App\Models\Facture::calculerMontant(20, 'Domestique'))
             ->isEqualTo(9000); // (10×350) + (10×550)
    }

    // ── Domestique tranche 3 : > 20 m³ à 780 FCFA ───────────────────────────

    public function testDomestiqueTrancheTrois()
    {
        $this->integer(\App\Models\Facture::calculerMontant(25, 'Domestique'))
             ->isEqualTo(12900); // (10×350) + (10×550) + (5×780)
    }

    // ── Professionnel : forfait 8500 + 950/m³ ───────────────────────────────

    public function testProfessionnel()
    {
        $this->integer(\App\Models\Facture::calculerMontant(25, 'Professionnel'))
             ->isEqualTo(32250); // 8500 + (25×950)
    }

    public function testProfessionnelPetiteConsommation()
    {
        $this->integer(\App\Models\Facture::calculerMontant(1, 'Professionnel'))
             ->isEqualTo(9450); // 8500 + (1×950)
    }

    // ── Cas d'erreur ─────────────────────────────────────────────────────────

    public function testConsommationNegativeLanceException()
    {
        $this->exception(function () {
            \App\Models\Facture::calculerMontant(-5, 'Domestique');
        })->isInstanceOf(\InvalidArgumentException::class);
    }

    public function testConsommationZeroLanceException()
    {
        $this->exception(function () {
            \App\Models\Facture::calculerMontant(0, 'Domestique');
        })->isInstanceOf(\InvalidArgumentException::class);
    }

    public function testTypeAbonnementInvalideLanceException()
    {
        $this->exception(function () {
            \App\Models\Facture::calculerMontant(10, 'Inconnu');
        })->isInstanceOf(\InvalidArgumentException::class);
    }
}
