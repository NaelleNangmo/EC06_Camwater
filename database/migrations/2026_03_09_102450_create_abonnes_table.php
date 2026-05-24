<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations pour créer la table des abonnés.
     * Cette table stocke les informations des clients de CAMWATER PRO.
     */
    public function up(): void
    {
        Schema::create('abonnes', function (Blueprint $table) {
            $table->id();
            // Informations personnelles de l'abonné
            $table->string('nom')->nullable(false)->comment('Nom de famille de l\'abonné');
            $table->string('prenom')->nullable(false)->comment('Prénom de l\'abonné');
            
            // Localisation
            $table->enum('ville', ['Yaoundé', 'Douala', 'Bafoussam', 'Garoua'])
                  ->comment('Ville de résidence de l\'abonné');
            $table->string('quartier')->comment('Quartier de résidence');
            
            // Informations du compteur
            $table->string('numero_compteur')->unique()->nullable(false)
                  ->comment('Numéro unique du compteur d\'eau');
            
            // Type d'abonnement
            $table->enum('type_abonnement', ['Domestique', 'Professionnel'])
                  ->comment('Type d\'abonnement : Domestique ou Professionnel');
            
            // Date de création
            $table->timestamp('date_creation')->useCurrent()
                  ->comment('Date de création du compte abonné');
            
            $table->timestamps();
        });
    }

    /**
     * Annule les migrations en supprimant la table des abonnés.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnes');
    }
};
