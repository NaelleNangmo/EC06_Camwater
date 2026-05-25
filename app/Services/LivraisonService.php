<?php

namespace App\Services;

// Service metier — calcul distance et suivi livraison
class LivraisonService
{
    /**
     * Calcul de distance simplifie entre deux adresses.
     * Utilise le tri alphabetique pour garantir la symetrie (distance(A,B) == distance(B,A)).
     */
    public function calculerDistance(string $depart, string $arrivee): float
    {
        if ($depart === $arrivee) {
            return 0.0;
        }

        $adresses = [$depart, $arrivee];
        sort($adresses);

        return round(abs(crc32($adresses[0]) - crc32($adresses[1])) / 1e8, 2);
    }
}
