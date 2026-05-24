<?php

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../vendor/simpletest/simpletest/src/autorun.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Abonne;

/**
 * Tests SimpleTest pour le modèle Abonne.
 */
class AbonneSimpleTest extends UnitTestCase
{
    public function testNomCompletRetournePrenomsEtNom()
    {
        $abonne = new Abonne();
        $abonne->nom    = 'Kamga';
        $abonne->prenom = 'Jean';

        $this->assertEqual('Jean Kamga', $abonne->nom_complet);
    }

    public function testNomCompletAvecPrenomCompose()
    {
        $abonne = new Abonne();
        $abonne->nom    = 'Nkolo';
        $abonne->prenom = 'Marie Claire';

        $this->assertEqual('Marie Claire Nkolo', $abonne->nom_complet);
    }

    public function testFillableContientChampsPrincipaux()
    {
        $abonne   = new Abonne();
        $fillable = $abonne->getFillable();

        $this->assertTrue(in_array('nom', $fillable));
        $this->assertTrue(in_array('prenom', $fillable));
        $this->assertTrue(in_array('ville', $fillable));
        $this->assertTrue(in_array('numero_compteur', $fillable));
        $this->assertTrue(in_array('type_abonnement', $fillable));
    }

    public function testAssignationAttributs()
    {
        $abonne = new Abonne([
            'nom'             => 'Biya',
            'prenom'          => 'Paul',
            'ville'           => 'Yaoundé',
            'quartier'        => 'Etoudi',
            'numero_compteur' => 'CPT-001',
            'type_abonnement' => 'Domestique',
        ]);

        $this->assertEqual('Biya', $abonne->nom);
        $this->assertEqual('Yaoundé', $abonne->ville);
        $this->assertEqual('Domestique', $abonne->type_abonnement);
    }
}
