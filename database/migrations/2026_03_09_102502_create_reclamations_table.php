<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Exécute les migrations pour créer la table des réclamations.
     * Cette table stocke les réclamations des abonnés concernant leurs factures.
     */
    public function up(): void
    {
        Schema::create('reclamations', function (Blueprint $table) {
            $table->id();
            
            // Référence vers la facture concernée
            $table->foreignId('facture_id')
                  ->constrained('factures')
                  ->onDelete('cascade')
                  ->comment('Identifiant de la facture contestée');
            
            // Statut de la réclamation
            $table->enum('statut', ['En attente', 'En cours', 'Resolue'])
                  ->default('En attente')
                  ->comment('Statut de traitement de la réclamation');
            
            // Réponse à la réclamation
            $table->text('reponse')->nullable()
                  ->comment('Réponse apportée à la réclamation');
            
            // Date de création
            $table->timestamp('date_creation')->useCurrent()
                  ->comment('Date de création de la réclamation');
            
            $table->timestamps();
        });
    }

    /**
     * Annule les migrations en supprimant la table des réclamations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamations');
    }
};
