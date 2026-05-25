<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create("livreurs", function (Blueprint $t) {
            $t->id(); $t->string("nom"); $t->string("prenom");
            $t->string("email")->unique(); $t->string("password");
            $t->string("telephone")->nullable(); $t->enum("statut", ["actif","inactif"])->default("actif");
            $t->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists("livreurs"); }
};
