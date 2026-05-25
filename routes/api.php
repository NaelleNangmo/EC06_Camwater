<?php
use App\Http\Controllers\AuthLivreurController;
use App\Http\Controllers\LivraisonController;
use Illuminate\Support\Facades\Route;

Route::post("/auth/login",  [AuthLivreurController::class, "login"]);
Route::middleware("auth:sanctum")->group(function () {
    Route::post("/auth/logout", [AuthLivreurController::class, "logout"]);
    Route::apiResource("livraisons", LivraisonController::class);
    Route::get("livraisons/{livraison}/suivi", [LivraisonController::class, "suivi"]);
});
