<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use MongoDB\BSON\UTCDateTime;

class MongoLogService
{
    /**
     * Enregistrer une activité dans MongoDB
     */
    public function logActivity(string $typeAction, int $operateurId, ?int $abonneId = null, array $details = []): void
    {
        // Vérifier si MongoDB est disponible
        if (!extension_loaded('mongodb')) {
            // MongoDB n'est pas installé, on log dans Laravel
            \Log::info('MongoDB Log (extension non installée)', [
                'type_action' => $typeAction,
                'operateur_id' => $operateurId,
                'abonne_id' => $abonneId,
                'details' => $details,
            ]);
            return;
        }

        try {
            DB::connection('mongodb')
                ->collection('activites')
                ->insert([
                    'type_action' => $typeAction,
                    'operateur_id' => $operateurId,
                    'abonne_id' => $abonneId,
                    'timestamp' => new UTCDateTime(),
                    'details' => $details,
                ]);
        } catch (\Exception $e) {
            // Log l'erreur sans interrompre l'application
            \Log::error('Erreur lors de l\'enregistrement du log MongoDB: ' . $e->getMessage());
        }
    }

    /**
     * Récupérer les logs d'un opérateur sur les 7 derniers jours
     */
    public function getOperateurLogs(int $operateurId, int $days = 7): array
    {
        try {
            $dateLimite = new UTCDateTime((time() - ($days * 24 * 60 * 60)) * 1000);

            $logs = DB::connection('mongodb')
                ->collection('activites')
                ->where('operateur_id', $operateurId)
                ->where('timestamp', '>=', $dateLimite)
                ->orderBy('timestamp', 'desc')
                ->get()
                ->toArray();

            return $logs;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des logs MongoDB: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer tous les logs d'un abonné
     */
    public function getAbonneLogs(int $abonneId): array
    {
        try {
            $logs = DB::connection('mongodb')
                ->collection('activites')
                ->where('abonne_id', $abonneId)
                ->orderBy('timestamp', 'desc')
                ->get()
                ->toArray();

            return $logs;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des logs de l\'abonné: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Statistiques par type d'action
     */
    public function getStatistiquesByType(): array
    {
        try {
            $stats = DB::connection('mongodb')
                ->collection('activites')
                ->raw(function ($collection) {
                    return $collection->aggregate([
                        [
                            '$group' => [
                                '_id' => '$type_action',
                                'total' => ['$sum' => 1],
                                'derniere_action' => ['$max' => '$timestamp']
                            ]
                        ],
                        [
                            '$sort' => ['total' => -1]
                        ]
                    ]);
                })
                ->toArray();

            return $stats;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            return [];
        }
    }
}
