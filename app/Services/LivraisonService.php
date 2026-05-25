<?php
namespace App\Services;
use App\Models\Livraison;
// Service metier  calcul distance et suivi livraison
class LivraisonService
{
    public function creer(array $data): Livraison
    {
        $data["distance_km"] = $this->calculerDistance($data["adresse_depart"], $data["adresse_arrivee"]);
        return Livraison::create($data);
    }
    public function suivi(Livraison $livraison): array
    {
        return ["reference" => $livraison->reference, "statut" => $livraison->statut, "distance_km" => $livraison->distance_km];
    }
    public function calculerDistance(string $depart, string $arrivee): float
    {
        // Calcul simplifie  a remplacer par une API de geolocalisation
        return round(abs(crc32($depart) - crc32($arrivee)) / 1e8, 2);
    }
}
