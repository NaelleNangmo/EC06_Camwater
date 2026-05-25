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
        return [
            "reference"   => $livraison->reference,
            "statut"      => $livraison->statut,
            "distance_km" => $livraison->distance_km,
        ];
    }

    /**
     * Calcul de distance simplifie entre deux adresses.
     * Utilise la valeur absolue de la difference des hash pour garantir
     * la symetrie (distance(A,B) == distance(B,A)).
     */
    public function calculerDistance(string $depart, string $arrivee): float
    {
        if ($depart === $arrivee) {
            return 0.0;
        }
        // Tri alphabetique pour garantir la symetrie
        $adresses = [$depart, $arrivee];
        sort($adresses);
        return round(abs(crc32($adresses[0]) - crc32($adresses[1])) / 1e8, 2);
    }
}
