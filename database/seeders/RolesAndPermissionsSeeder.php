<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'manage-users',
            'create-admins',
            'delete-admins',
            'manage-blog',
            'manage-activities',
            'manage-courses',
        ];

        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        // Create roles and assign permissions
        $rootRole = Role::findOrCreate('root');
        $rootRole->syncPermissions($permissions);

        $adminRole = Role::findOrCreate('admin');
        $adminRole->syncPermissions([
            'manage-users',
            'manage-blog',
            'manage-activities',
            'manage-courses',
        ]);

        $editorRole = Role::findOrCreate('editor');
        $editorRole->syncPermissions([
            'manage-blog',
        ]);

        // Assign root role to the root user
        $rootUser = User::where('email', 'like', 'rosarioedwinac%')->first();
        if ($rootUser) {
            $rootUser->assignRole($rootRole);
        } else {
            $rootUser = User::create([
                'name' => 'Edwin Rosario (Root)',
                'email' => 'rosarioedwinac@gmail.com',
                'password' => bcrypt('password'),
            ]);
            $rootUser->assignRole($rootRole);
        }

        // Assign admin role to the admin user
        $adminUser = User::where('email', 'admin@fudounar.org')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        }
    }
}
