<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations pour créer la table des opérateurs.
     * Cette table stocke les comptes des opérateurs du système.
     */
    public function up(): void
    {
        Schema::create('operateurs', function (Blueprint $table) {
            $table->id();

            // Identifiants de connexion
            $table->string('login')->unique()
                ->comment('Identifiant de connexion de l\'opérateur');

            // Mot de passe haché
            $table->string('password')
                ->comment('Mot de passe haché de l\'opérateur');

            // Rôle de l'opérateur
            $table->string('role')
                ->comment('Rôle de l\'opérateur dans le système');

            $table->timestamps();
        });
    }

    /**
     * Annule les migrations en supprimant la table des opérateurs.
     */
    public function down(): void
    {
        Schema::dropIfExists('operateurs');
    }
};
