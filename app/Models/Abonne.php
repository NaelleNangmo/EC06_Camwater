<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle représentant un abonné de CAMWATER.
 * Un abonné est un client qui possède un compteur d'eau.
 */
class Abonne extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'abonnes';

    /**
     * Les attributs qui peuvent être assignés en masse.
     */
    protected $fillable = [
        'nom',
        'prenom',
        'ville',
        'quartier',
        'numero_compteur',
        'type_abonnement',
        'date_creation',
    ];

    /**
     * Les attributs qui doivent être castés en types natifs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_creation' => 'datetime',
    ];

    /**
     * Relation : Un abonné possède plusieurs factures.
     */
    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class, 'abonne_id');
    }

    /**
     * Accesseur pour obtenir le nom complet de l'abonné.
     */
    public function getNomCompletAttribute(): string
    {
        return "{$this->prenom} {$this->nom}";
    }
}
