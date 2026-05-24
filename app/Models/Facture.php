<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle représentant une facture d'eau.
 * Une facture est générée pour un abonné en fonction de sa consommation.
 */
class Facture extends Model
{
    use HasFactory;
    /**
     * Le nom de la table associée au modèle.
     *
     */
    protected $table = 'factures';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'abonne_id',
        'consommation',
        'montant_total',
        'date_emission',
        'statut',
    ];

    /**
     * Les attributs qui doivent être castés en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'consommation' => 'decimal:2',
        'montant_total' => 'decimal:2',
        'date_emission' => 'datetime',
    ];

    /**
     * Relation : Une facture appartient à un abonné.
     *
     * @return BelongsTo
     */
    public function abonne(): BelongsTo
    {
        return $this->belongsTo(Abonne::class, 'abonne_id');
    }

    /**
     * Relation : Une facture peut avoir plusieurs réclamations.
     *
     * @return HasMany
     */
    public function reclamations(): HasMany
    {
        return $this->hasMany(Reclamation::class, 'facture_id');
    }

    /**
     * Méthode pour calculer le montant total d'une facture.
     * Le calcul dépend du type d'abonnement et de la consommation.
     */
    public static function calculerMontant(float $consommation, string $typeAbonnement): int
    {
        // Vérifier que la consommation est positive
        if ($consommation <= 0) {
            throw new \InvalidArgumentException('La consommation doit être strictement positive.');
        }

        $montant = 0;

        if ($typeAbonnement === 'Domestique') {
            // Tarification pour les abonnés domestiques
            if ($consommation <= 10) {
                // 0-10 m³ : 350 FCFA par m³
                $montant = $consommation * 350;
            } elseif ($consommation <= 20) {
                // 11-20 m³ : 550 FCFA par m³
                $montant = (10 * 350) + (($consommation - 10) * 550);
            } else {
                // Plus de 20 m³ : 780 FCFA par m³
                $montant = (10 * 350) + (10 * 550) + (($consommation - 20) * 780);
            }
        } elseif ($typeAbonnement === 'Professionnel') {
            // Tarification pour les abonnés professionnels
            // Forfait de base : 8500 FCFA + 950 FCFA par m³
            $montant = 8500 + ($consommation * 950);
        } else {
            throw new \InvalidArgumentException('Type d\'abonnement invalide. Doit être "Domestique" ou "Professionnel".');
        }

        // Arrondir au FCFA supérieur et convertir en entier
        return (int) ceil($montant);
    }
}
