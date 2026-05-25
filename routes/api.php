<?php
use App\Http\Controllers\AuthLivreurController;
use App\Http\Controllers\LivraisonController;
use Illuminate\Support\Facades\Route;

// Auth
Route::post("/auth/login",  [AuthLivreurController::class, "login"]);

// Routes protegees
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/auth/logout", [AuthLivreurController::class, "logout"]);

    // Livraisons  CRUD + suivi
    Route::apiResource("livraisons", LivraisonController::class)
         ->only(["index", "store", "show"]);
    Route::get("livraisons/{livraison}/suivi", [LivraisonController::class, "suivi"]);
});
