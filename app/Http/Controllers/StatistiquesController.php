<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Services\MongoLogService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatistiquesController extends Controller
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

        /**
     * Affiche la liste de toutes les factures avec pagination.
     *
     * @return JsonResponse
     */

        $nbr= function() use ($factures){
            $factures->count();
        };



        /**
     * Affiche la liste de toutes les factures avec pagination.
     *
     * @return JsonResponse
     */

        return response()->json([
            'success' => true,
            'Stats' => [
                'total' => $factures->total(),
                'Douala' => 3,
                'Yaounde'=> 3,
                'Garoua'=>1,
                'Bafoussam'=> 2,
            ],
            'message' => 'Les statistiques des factures'
        ], 200);
    }


    /**
     * Affiche les détails d'une facture spécifique.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {

    }

    public function update(Request $request, string $id)
    {

    }


    public function destroy(string $id)
    {
    }

}
