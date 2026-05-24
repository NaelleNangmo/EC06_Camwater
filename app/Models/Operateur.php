<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * Modèle représentant un opérateur du système.
 * Un opérateur est un utilisateur qui gère les abonnés et les factures.
 */
class Operateur extends Model
{
    /**
     * Le nom de la table associée au modèle.
     *
     */
    protected $table = 'operateurs';

    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login',
        'password',
        'role',
    ];

    /**
     * Les attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Mutateur pour hacher automatiquement le mot de passe.
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
