<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
// Modele Livraison  gestion des livraisons
class Livraison extends Model
{
    protected $fillable = ["reference","adresse_depart","adresse_arrivee","statut","distance_km","livreur_id"];
    public function livreur() { return $this->belongsTo(Livreur::class); }
}
