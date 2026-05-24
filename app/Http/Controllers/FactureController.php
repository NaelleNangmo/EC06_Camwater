<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use App\Models\Facture;
use App\Services\MongoLogService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur pour gérer les opérations sur les factures.
 */
class FactureController extends Controller
{
    protected $mongoLog;
    protected $cache;

    public function __construct(MongoLogService $mongoLog, CacheService $cache)
    {
        $this->mongoLog = $mongoLog;
        $this->cache = $cache;
    }

    /**
     * Affiche la liste de toutes les factures avec pagination.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Utiliser le cache pour améliorer les performances
        $page = request()->get('page', 1);
        $cacheKey = "factures_page_{$page}";

        $factures = $this->cache->remember($cacheKey, function () {
            return Facture::with('abonne')->paginate(15);
        }, 30); // Cache de 30 minutes

        return response()->json([
            'success' => true,
            'data' => $factures->items(),
            'pagination' => [
                'total' => $factures->total(),
                'per_page' => $factures->perPage(),
                'current_page' => $factures->currentPage(),
                'last_page' => $factures->lastPage(),
                'from' => $factures->firstItem(),
                'to' => $factures->lastItem(),
            ],
            'message' => 'Liste des factures récupérée avec succès.'
        ], 200);
    }

    /**
     * Génère une nouvelle facture pour un abonné.
     * Cette méthode calcule automatiquement le montant en fonction
     * de la consommation et du type d'abonnement.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generer(Request $request): JsonResponse
    {
        // Validation des données reçues
        $validated = $request->validate([
            'abonne_id' => 'required|exists:abonnes,id',
            'consommation' => 'required|numeric|min:0.01',
        ]);

        // Récupérer l'abonné
        $abonne = Abonne::findOrFail($validated['abonne_id']);

        try {
            // Calculer le montant total de la facture
            $montantTotal = Facture::calculerMontant(
                $validated['consommation'],
                $abonne->type_abonnement
            );

            // Créer la facture
            $facture = Facture::create([
                'abonne_id' => $validated['abonne_id'],
                'consommation' => $validated['consommation'],
                'montant_total' => $montantTotal,
                'statut' => 'Emise',
            ]);

            // Charger la relation avec l'abonné
            $facture->load('abonne');

            // Invalider le cache des factures
            $this->cache->forgetByPattern('factures_*');

            // Logger l'action dans MongoDB
            $this->mongoLog->logActivity(
                'generation_facture',
                1, // l'ID de l'opérateur authentifié
                $abonne->id,
                [
                    'consommation' => $facture->consommation,
                    'montant' => $facture->montant_total,
                    'type_abonnement' => $abonne->type_abonnement,
                    'numero_compteur' => $abonne->numero_compteur
                ]
            );

            return response()->json([
                'success' => true,
                'data' => $facture,
                'message' => 'Facture générée avec succès.'
            ], 201);

        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Affiche les détails d'une facture spécifique.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        // Récupérer la facture avec l'abonné et les réclamations
        $facture = Facture::with(['abonne', 'reclamations'])->find($id);

        if (!$facture) {
            return response()->json([
                'success' => false,
                'message' => 'Facture non trouvée.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $facture,
            'message' => 'Détails de la facture récupérés avec succès.'
        ], 200);
    }

    /**
     * Met à jour le statut d'une facture.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Récupérer la facture
        $facture = Facture::find($id);

        if (!$facture) {
            return response()->json([
                'success' => false,
                'message' => 'Facture non trouvée.'
            ], 404);
        }

        // Validation des données
        $validated = $request->validate([
            'statut' => 'required|in:Emise,Payee',
        ]);

        // Sauvegarder l'ancien statut
        $ancienStatut = $facture->statut;

        // Mettre à jour le statut
        $facture->update($validated);

        // Invalider le cache des factures
        $this->cache->forgetByPattern('factures_*');

        // Logger l'action dans MongoDB si le statut est "Payee"
        if ($validated['statut'] === 'Payee') {
            $this->mongoLog->logActivity(
                'paiement_facture',
                1, // l'ID de l'opérateur authentifié
                $facture->abonne_id,
                [
                    'facture_id' => $facture->id,
                    'montant' => $facture->montant_total,
                    'mode_paiement' => 'Non spécifié',
                    'ancien_statut' => $ancienStatut
                ]
            );
        }

        return response()->json([
            'success' => true,
            'data' => $facture,
            'message' => 'Statut de la facture mis à jour avec succès.'
        ], 200);
    }

    /**
     * Supprime une facture de la base de données.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        // Récupérer la facture
        $facture = Facture::find($id);

        if (!$facture) {
            return response()->json([
                'success' => false,
                'message' => 'Facture non trouvée.'
            ], 404);
        }

        // Supprimer la facture
        $facture->delete();

        // Invalider le cache des factures
        $this->cache->forgetByPattern('factures_*');

        return response()->json([
            'success' => true,
            'message' => 'Facture supprimée avec succès.'
        ], 200);
    }
}
