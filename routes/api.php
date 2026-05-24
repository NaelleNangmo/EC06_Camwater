<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbonneController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\StatistiquesController;

/**
 * Routes API pour l'application CAMWATER PRO.
 * Toutes ces routes sont préfixées par /api et utilisent le middleware 'api'.
 *
 * Rate Limiting :
 * - Routes publiques : 60 requêtes/minute
 * - Routes authentifiées : 120 requêtes/minute
 */

// ============================================================================
// ROUTES PUBLIQUES (sans authentification)
// Rate limit : 60 requêtes/minute
// ============================================================================

Route::middleware(['throttle:60,1'])->group(function () {
    // Authentification
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // Routes de consultation publiques
    Route::get('abonnes', [AbonneController::class, 'index'])->name('abonnes.index');
    Route::get('abonnes/{id}', [AbonneController::class, 'show'])->name('abonnes.show');
    Route::get('factures', [FactureController::class, 'index'])->name('factures.index');
    Route::get('factures/{id}', [FactureController::class, 'show'])->name('factures.show');
    Route::post('reclamations', [ReclamationController::class, 'store'])->name('reclamations.store');
});

// ============================================================================
// ROUTES PROTÉGÉES (authentification requise)
// Rate limit : 120 requêtes/minute
// ============================================================================

Route::middleware(['auth:sanctum', 'throttle:120,1'])->group(function () {

    // Authentification
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('me', [AuthController::class, 'me'])->name('me');

    // Gestion des abonnés (création, modification, suppression)
    Route::post('abonnes', [AbonneController::class, 'store'])->name('abonnes.store');
    Route::put('abonnes/{id}', [AbonneController::class, 'update'])->name('abonnes.update');
    Route::delete('abonnes/{id}', [AbonneController::class, 'destroy'])->name('abonnes.destroy');

    // Gestion des factures (création, modification, suppression)
    Route::post('factures/generer', [FactureController::class, 'generer'])->name('factures.generer');
    Route::put('factures/{id}', [FactureController::class, 'update'])->name('factures.update');
    Route::delete('factures/{id}', [FactureController::class, 'destroy'])->name('factures.destroy');

    // Gestion des reclamations
    Route::get('reclamations', [ReclamationController::class, 'index'])->name('reclamations.index');
    Route::get('reclamations/{id}', [ReclamationController::class, 'show'])->name('reclamations.show');
    Route::put('reclamations/{id}', [ReclamationController::class, 'update'])->name('reclamations.update');
    Route::delete('reclamations/{id}', [ReclamationController::class, 'destroy'])->name('reclamations.destroy');

    //Gestion des statistiques
    Route::get('statistiques', [StatistiquesController::class, 'index'])->name('reclamations.index');

});
