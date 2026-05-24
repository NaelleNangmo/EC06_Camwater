<?php

namespace Database\Factories;

use App\Models\Abonne;
use App\Models\Facture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory pour générer des factures de test.
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Facture>
 */
class FactureFactory extends Factory
{
    protected $model = Facture::class;

    /**
     * Définit l'état par défaut du modèle.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $consommation = $this->faker->randomFloat(2, 5, 50);
        
        // Créer un abonné si aucun n'est fourni
        $abonne = Abonne::factory()->create();
        
        // Calculer le montant selon le type d'abonnement
        $montant = Facture::calculerMontant($consommation, $abonne->type_abonnement);

        return [
            'abonne_id' => $abonne->id,
            'consommation' => $consommation,
            'montant_total' => $montant,
            'date_emission' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'statut' => $this->faker->randomElement(['Emise', 'Payee']),
        ];
    }

    /**
     * Indique que la facture est émise (non payée).
     */
    public function emise(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Emise',
        ]);
    }

    /**
     * Indique que la facture est payée.
     */
    public function payee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut' => 'Payee',
        ]);
    }

    /**
     * Définit un abonné spécifique pour la facture.
     */
    public function forAbonne(Abonne $abonne): static
    {
        return $this->state(function (array $attributes) use ($abonne) {
            $consommation = $attributes['consommation'] ?? $this->faker->randomFloat(2, 5, 50);
            $montant = Facture::calculerMontant($consommation, $abonne->type_abonnement);
            
            return [
                'abonne_id' => $abonne->id,
                'montant_total' => $montant,
            ];
        });
    }
}
