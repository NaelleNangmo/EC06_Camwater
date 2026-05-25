<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
// Modele Livreur  authentification livreur
class Livreur extends Authenticatable
{
    protected $fillable = ["nom","prenom","email","password","telephone","statut"];
    protected $hidden   = ["password","remember_token"];
    public function livraisons() { return $this->hasMany(Livraison::class); }
}
