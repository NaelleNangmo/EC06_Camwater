<?php

namespace App\Http\Controllers;

use App\Models\Abonne;
use App\Services\CacheService;
use App\Services\MongoLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Contrôleur pour gérer les opérations CRUD sur les abonnés.
 */
class AbonneController extends Controller
{
    protected $mongoLog;

    protected $cache;

    public function __construct(MongoLogService $mongoLog, CacheService $cache)
    {
        $this->mongoLog = $mongoLog;
        $this->cache = $cache;
    }

    /**
     * Affiche la liste de tous les abonnés avec pagination.
     */
    public function index(): JsonResponse
    {
        // Utiliser le cache pour améliorer les performances
        $page = request()->get('page', 1);
        $cacheKey = "abonnes_page_{$page}";

        $abonnes = $this->cache->remember($cacheKey, function () {
            return Abonne::with('factures')->paginate(15);
        }, 30); // Cache de 30 minutes

        return response()->json([
            'success' => true,
            'data' => $abonnes->items(),
            'pagination' => [
                'total' => $abonnes->total(),
                'per_page' => $abonnes->perPage(),
                'current_page' => $abonnes->currentPage(),
                'last_page' => $abonnes->lastPage(),
                'from' => $abonnes->firstItem(),
                'to' => $abonnes->lastItem(),
            ],
            'message' => 'Liste des abonnés récupérée avec succès.',
        ], 200);
    }

    /**
     * Crée un nouvel abonné dans la base de données.
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données reçues
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'ville' => ['required', Rule::in(['Yaoundé', 'Douala', 'Bafoussam', 'Garoua'])],
            'quartier' => 'required|string|max:255',
            'numero_compteur' => 'required|string|unique:abonnes,numero_compteur',
            'type_abonnement' => ['required', Rule::in(['Domestique', 'Professionnel'])],
        ]);

        // Créer l'abonné
        $abonne = Abonne::create($validated);

        // Invalider le cache des abonnés
        $this->cache->forgetByPattern('abonnes_*');

        // Logger l'action dans MongoDB
        $this->mongoLog->logActivity(
            'creation_abonne',
            1, //  l'ID de l'opérateur authentifié
            $abonne->id,
            [
                'nom' => $abonne->nom,
                'prenom' => $abonne->prenom,
                'ville' => $abonne->ville,
                'quartier' => $abonne->quartier,
                'numero_compteur' => $abonne->numero_compteur,
                'type_abonnement' => $abonne->type_abonnement,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $abonne,
            'message' => 'Abonné créé avec succès.',
        ], 201);
    }

    /**
     * Affiche les détails d'un abonné spécifique.
     */
    public function show(string $id): JsonResponse
    {
        // Récupérer l'abonné avec ses factures
        $abonne = Abonne::with('factures')->find($id);

        if (! $abonne) {
            return response()->json([
                'success' => false,
                'message' => 'Abonné non trouvé.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $abonne,
            'message' => 'Détails de l\'abonné récupérés avec succès.',
        ], 200);
    }

    /**
     * Met à jour les informations d'un abonné.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Récupérer l'abonné
        $abonne = Abonne::find($id);

        if (! $abonne) {
            return response()->json([
                'success' => false,
                'message' => 'Abonné non trouvé.',
            ], 404);
        }

        // Validation des données
        $validated = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'ville' => ['sometimes', Rule::in(['Yaoundé', 'Douala', 'Bafoussam', 'Garoua'])],
            'quartier' => 'sometimes|string|max:255',
            'numero_compteur' => 'sometimes|string|unique:abonnes,numero_compteur,'.$id,
            'type_abonnement' => ['sometimes', Rule::in(['Domestique', 'Professionnel'])],
        ]);

        // Sauvegarder les anciennes valeurs pour le log
        $anciennesValeurs = $abonne->only(array_keys($validated));

        // Mettre à jour l'abonné
        $abonne->update($validated);

        // Invalider le cache des abonnés
        $this->cache->forgetByPattern('abonnes_*');

        // Logger l'action dans MongoDB
        $this->mongoLog->logActivity(
            'modification_abonne',
            1, // l'ID de l'opérateur authentifié
            $abonne->id,
            [
                'champs_modifies' => array_keys($validated),
                'anciennes_valeurs' => $anciennesValeurs,
                'nouvelles_valeurs' => $validated,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $abonne,
            'message' => 'Abonné mis à jour avec succès.',
        ], 200);
    }

    /**
     * Supprime un abonné de la base de données.
     */
    public function destroy(string $id): JsonResponse
    {
        // Récupérer l'abonné
        $abonne = Abonne::find($id);

        if (! $abonne) {
            return response()->json([
                'success' => false,
                'message' => 'Abonné non trouvé.',
            ], 404);
        }

        // Logger l'action avant suppression
        $this->mongoLog->logActivity(
            'suppression_abonne',
            1, // l'ID de l'opérateur authentifié
            $abonne->id,
            [
                'nom_complet' => $abonne->nom.' '.$abonne->prenom,
                'numero_compteur' => $abonne->numero_compteur,
                'raison' => 'Suppression via API',
            ]
        );

        // Supprimer l'abonné (les factures seront supprimées en cascade)
        $abonne->delete();

        // Invalider le cache des abonnés
        $this->cache->forgetByPattern('abonnes_*');

        return response()->json([
            'success' => true,
            'message' => 'Abonné supprimé avec succès.',
        ], 200);
    }
}
