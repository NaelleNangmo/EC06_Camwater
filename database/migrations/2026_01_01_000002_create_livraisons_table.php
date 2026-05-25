<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create("livraisons", function (Blueprint $t) {
            $t->id(); $t->string("reference")->unique();
            $t->string("adresse_depart"); $t->string("adresse_arrivee");
            $t->enum("statut", ["en_attente","en_cours","livree","annulee"])->default("en_attente");
            $t->decimal("distance_km", 8, 2)->default(0);
            $t->foreignId("livreur_id")->nullable()->constrained("livreurs")->nullOnDelete();
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("livraisons"); }
};
