<?php

namespace App\Http\Controllers;
use App\Models\Reclamation;
use App\Services\MongoLogService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;


class ReclamationController extends Controller
{
    protected $mongoLog;
    protected $cache;

    public function __construct(MongoLogService $mongoLog, CacheService $cache)
    {
        $this->mongoLog = $mongoLog;
        $this->cache = $cache;
    }
    /**
     * Affiche la liste de tous les reclamations avec pagination.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        // Utiliser le cache pour améliorer les performances
        $page = request()->get('page', 1);
        $cacheKey = "reclamation_page_{$page}";

        $reclamation = $this->cache->remember($cacheKey, function () {
            return Reclamation::with('factures')->paginate(15);
        }, 30); // Cache de 30 minutes

        return response()->json([
            'success' => true,
            'data' => $reclamation->items(),
            'pagination' => [
                'total' => $reclamation->total(),
                'per_page' => $reclamation->perPage(),
                'current_page' => $reclamation->currentPage(),
                'last_page' => $reclamation->lastPage(),
                'from' => $reclamation->firstItem(),
                'to' => $reclamation->lastItem(),
            ],
            'message' => 'Liste des reclamations récupérée avec succès.'
        ], 200);
    }

    /**
     * Crée un nouvel reclamation dans la base de données.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des données reçues
        $validated = $request->validate([
            'facture_id' => 'required|int',
            'statut' => ['required', Rule::in(['En attente', 'En cours', 'Resolue'])],
            'reponse' => 'string|max:255',
        ]);

        // Créer l'reclamation
        $reclamation = Reclamation::create($validated);

        // Invalider le cache des reclamations
        $this->cache->forgetByPattern('reclamation_*');

        // Logger l'action dans MongoDB
        $this->mongoLog->logActivity(
            'creation_reclamation',
            1, //  l'ID de l'opérateur authentifié
            $reclamation->id,
            [
                'nom' => $reclamation->nom,
                'prenom' => $reclamation->prenom,
                'ville' => $reclamation->ville,
                'quartier' => $reclamation->quartier,
                'numero_compteur' => $reclamation->numero_compteur,
                'type_reclamationment' => $reclamation->type_reclamationment
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $reclamation,
            'message' => 'reclamation créé avec succès.'
        ], 201);
    }

    /**
     * Affiche les détails d'un reclamation spécifique.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        // Récupérer l'reclamation avec ses factures
        $reclamation = Reclamation::with('factures')->find($id);

        if (!$reclamation) {
            return response()->json([
                'success' => false,
                'message' => 'reclamation non trouvé.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $reclamation,
            'message' => 'Détails de la reclamation récupérés avec succès.'
        ], 200);
    }

    /**
     * Met à jour les informations d'un reclamation.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Récupérer la reclamation
        $reclamation = Reclamation::find($id);

        if (!$reclamation) {
            return response()->json([
                'success' => false,
                'message' => 'reclamation non trouvé.'
            ], 404);
        }

        // Validation des données
        $validated = $request->validate([
            'statut' => ['sometimes', Rule::in(['En attente', 'En cours', 'Resolue'])],
            'reponse' => 'sometimes|string|max:255',

        ]);

        // Sauvegarder les anciennes valeurs pour le log
        $anciennesValeurs = $reclamation->only(array_keys($validated));

        // Mettre à jour l'reclamation
        $reclamation->update($validated);

        // Invalider le cache des reclamations
        $this->cache->forgetByPattern('reclamation_*');

        // Logger l'action dans MongoDB
        $this->mongoLog->logActivity(
            'modification_reclamation',
            1, // l'ID de l'opérateur authentifié
            $reclamation->id,
            [
                'champs_modifies' => array_keys($validated),
                'anciennes_valeurs' => $anciennesValeurs,
                'nouvelles_valeurs' => $validated
            ]
        );

        return response()->json([
            'success' => true,
            'data' => $reclamation,
            'message' => 'reclamation mis à jour avec succès.'
        ], 200);
    }

    /**
     * Supprime un reclamation de la base de données.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        // Récupérer l'reclamation
        $reclamation = Reclamation::find($id);

        if (!$reclamation) {
            return response()->json([
                'success' => false,
                'message' => 'reclamation non trouvé.'
            ], 404);
        }


        // Logger l'action avant suppression
        $this->mongoLog->logActivity(
            'suppression_reclamation',
            1, // l'ID de l'opérateur authentifié
            $reclamation->id,
            [
                'nom_complet' => $reclamation->nom . ' ' . $reclamation->prenom,
                'numero_compteur' => $reclamation->numero_compteur,
                'raison' => 'Suppression via API'
            ]
        );

        // Supprimer l'reclamation (les factures seront supprimées en cascade)
        $reclamation->delete();

        // Invalider le cache des reclamations
        $this->cache->forgetByPattern('reclamation_*');

        return response()->json([
            'success' => true,
            'message' => 'reclamation supprimé avec succès.'
        ], 200);
    }
}
