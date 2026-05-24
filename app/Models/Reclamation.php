<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * Modèle représentant une réclamation.
 * Une réclamation est déposée par un abonné concernant une facture.
 */
class Reclamation extends Model
{
    use HasFactory;
    /**
     * Le nom de la table associée au modèle.
     *
     */
    protected $table = 'reclamations';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'facture_id',
        'statut',
        'reponse',
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
     * Relation : Une réclamation appartient à une facture.
     *
     * @return BelongsTo
     */
    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class, 'facture_id');
    }


        public function factures(): HasMany
    {
        return $this->hasMany(Facture::class, 'id');
    }
}
