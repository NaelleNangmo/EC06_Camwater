<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Seeder pour créer les rôles et permissions du système CAMWATER PRO.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser le cache des permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Créer les permissions
        $permissions = [
            // Permissions Abonnés
            'view_abonnes',
            'create_abonnes',
            'edit_abonnes',
            'delete_abonnes',

            // Permissions Factures
            'view_factures',
            'create_factures',
            'edit_factures',
            'delete_factures',

            // Permissions Réclamations
            'view_reclamations',
            'create_reclamations',
            'edit_reclamations',
            'delete_reclamations',

            // Permissions Utilisateurs
            'manage_users',
            'view_logs',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Créer le rôle Admin avec toutes les permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Créer le rôle Opérateur avec permissions limitées
        $operateurRole = Role::create(['name' => 'operateur']);
        $operateurRole->givePermissionTo([
            'view_abonnes',
            'create_abonnes',
            'edit_abonnes',
            'view_factures',
            'create_factures',
            'edit_factures',
            'view_reclamations',
            'create_reclamations',
            'edit_reclamations',
        ]);

        // Créer le rôle Consultant (lecture seule)
        $consultantRole = Role::create(['name' => 'consultant']);
        $consultantRole->givePermissionTo([
            'view_abonnes',
            'view_factures',
            'view_reclamations',
        ]);

        // Assigner les rôles aux utilisateurs existants
        $admin = User::where('email', 'admin@camwater.cm')->first();
        if ($admin) {
            $admin->assignRole('admin');
        }

        $operateur = User::where('email', 'operateur@camwater.cm')->first();
        if ($operateur) {
            $operateur->assignRole('operateur');
        }

        $this->command->info('✅ Rôles et permissions créés avec succès !');
        $this->command->info('   - Admin : toutes les permissions');
        $this->command->info('   - Opérateur : permissions limitées');
        $this->command->info('   - Consultant : lecture seule');
    }
}
