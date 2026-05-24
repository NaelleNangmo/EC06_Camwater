<?php

namespace Database\Factories;

use App\Models\Facture;
use App\Models\Reclamation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory pour générer des réclamations de test.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reclamation>
 */
class ReclamationFactory extends Factory
{
    protected $model = Reclamation::class;

    /**
     * Définit l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statut = $this->faker->randomElement(['En attente', 'En cours', 'Resolue']);
        
        return [
            'facture_id' => Facture::factory(),
            'statut' => $statut,
            'reponse' => $statut === 'En attente' ? null : $this->faker->sentence(),
            'date_creation' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indique que la réclamation est en attente.
     */
    public function enAttente(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'En attente',
            'reponse' => null,
        ]);
    }

    /**
     * Indique que la réclamation est en cours.
     */
    public function enCours(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'En cours',
            'reponse' => $this->faker->sentence(),
        ]);
    }

    /**
     * Indique que la réclamation est résolue.
     */
    public function resolue(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Resolue',
            'reponse' => $this->faker->sentence(),
        ]);
    }
}
