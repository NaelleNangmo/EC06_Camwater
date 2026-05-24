<?php

namespace Database\Factories;

use App\Models\Abonne;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory pour générer des abonnés de test.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Abonne>
 */
class AbonneFactory extends Factory
{
    protected $model = Abonne::class;

    /**
     * Définit l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $villes = ['Yaoundé', 'Douala', 'Bafoussam', 'Garoua'];
        $ville = $this->faker->randomElement($villes);

        $prefixes = [
            'Yaoundé' => 'YAO',
            'Douala' => 'DLA',
            'Bafoussam' => 'BAF',
            'Garoua' => 'GAR',
        ];

        return [
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'ville' => $ville,
            'quartier' => $this->faker->streetName(),
            'numero_compteur' => 'CPT-'.$prefixes[$ville].'-'.$this->faker->unique()->numberBetween(1000, 9999),
            'type_abonnement' => $this->faker->randomElement(['Domestique', 'Professionnel']),
            'date_creation' => now(),
        ];
    }

    /**
     * Indique que l'abonné est de type Domestique.
     */
    public function domestique(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_abonnement' => 'Domestique',
        ]);
    }

    /**
     * Indique que l'abonné est de type Professionnel.
     */
    public function professionnel(): static
    {
        return $this->state(fn (array $attributes) => [
            'type_abonnement' => 'Professionnel',
        ]);
    }
}
