<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Exécute les migrations pour créer la table des factures.
     * Cette table stocke les factures d'eau générées pour les abonnés.
     */
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();

            // Référence vers l'abonné
            $table->foreignId('abonne_id')
                  ->constrained('abonnes')
                  ->onDelete('cascade')
                  ->comment('Identifiant de l\'abonné concerné');

            // Informations de consommation
            $table->decimal('consommation', 10, 2)
                  ->unsigned()
                  ->comment('Consommation d\'eau en mètres cubes (m³)');

            // Montant de la facture
            $table->decimal('montant_total', 10, 2)
                  ->comment('Montant total de la facture en FCFA');

            // Date d'émission
            $table->timestamp('date_emission')->useCurrent()
                  ->comment('Date d\'émission de la facture');

            // Statut de la facture
            $table->enum('statut', ['Emise', 'Payee'])
                  ->default('Emise')
                  ->comment('Statut de paiement de la facture');

            $table->timestamps();
        });

        // Ajouter la contrainte CHECK pour PostgreSQL
        if (config('database.default') === 'pgsql') {
            DB::statement('ALTER TABLE factures ADD CONSTRAINT factures_consommation_positive CHECK (consommation > 0)');
        }
    }

    /**
     * Annule les migrations en supprimant la table des factures.
     */
    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
