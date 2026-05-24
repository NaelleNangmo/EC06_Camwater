<?php

namespace Tests\Atoum\App\Models;

use atoum\atoum;

/**
 * Tests atoum pour App\Models\Abonne.
 * Teste les accesseurs et la logique pure sans base de données.
 *
 * @namespace Tests\Atoum
 */
class Abonne extends atoum\test
{
    // ── Accesseur nomComplet ─────────────────────────────────────────────────

    public function testNomCompletRetournePrenomsEtNom()
    {
        $abonne = new \App\Models\Abonne();
        $abonne->nom    = 'Kamga';
        $abonne->prenom = 'Jean';

        $this->string($abonne->nom_complet)
             ->isEqualTo('Jean Kamga');
    }

    public function testNomCompletAvecPrenomCompose()
    {
        $abonne = new \App\Models\Abonne();
        $abonne->nom    = 'Nkolo';
        $abonne->prenom = 'Marie Claire';

        $this->string($abonne->nom_complet)
             ->isEqualTo('Marie Claire Nkolo');
    }

    // ── Attributs fillable ───────────────────────────────────────────────────

    public function testFillableContientLesChampsPrincipaux()
    {
        $abonne   = new \App\Models\Abonne();
        $fillable = $abonne->getFillable();

        $this->array($fillable)
             ->contains('nom')
             ->contains('prenom')
             ->contains('ville')
             ->contains('numero_compteur')
             ->contains('type_abonnement');
    }

    // ── Assignation en masse ─────────────────────────────────────────────────

    public function testAssignationAttributs()
    {
        $abonne = new \App\Models\Abonne([
            'nom'             => 'Biya',
            'prenom'          => 'Paul',
            'ville'           => 'Yaoundé',
            'quartier'        => 'Etoudi',
            'numero_compteur' => 'CPT-001',
            'type_abonnement' => 'Domestique',
        ]);

        $this->string($abonne->nom)->isEqualTo('Biya');
        $this->string($abonne->ville)->isEqualTo('Yaoundé');
        $this->string($abonne->type_abonnement)->isEqualTo('Domestique');
    }
}
