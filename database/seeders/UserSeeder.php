<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Créer des utilisateurs de test pour l'API.
     */
    public function run(): void
    {
        // Créer un utilisateur admin
        User::create([
            'name' => 'Admin CAMWATER',
            'email' => 'admin@camwater.cm',
            'password' => Hash::make('password123'),
        ]);

        // Créer un utilisateur opérateur
        User::create([
            'name' => 'Opérateur Test',
            'email' => 'operateur@camwater.cm',
            'password' => Hash::make('password123'),
        ]);

        echo "✓ Utilisateurs créés avec succès\n";
        echo "  - admin@camwater.cm (password: password123)\n";
        echo "  - operateur@camwater.cm (password: password123)\n";
    }
}
