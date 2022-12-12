<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        User::all()->each(function (User $user) {
            setPermissionsTeamId($user->company_id);

            // create permissions
            foreach (Role::DEFAULT_ROLES as $role) {
                $roleModel = Role::findOrCreate($role['name']);
                $allPermissions = [];
                foreach ($role['permissions'] as $model => $permissions) {
                    foreach ($permissions as $permission) {
                        $allPermissions[] = Permission::findOrCreate($permission . ' ' . $model);
                    }
                }
                $roleModel->syncPermissions($allPermissions);
            }

            switch ($user->role) {
                case 'user':
                case 'wash_attendant':
                    $user->assignRole('User');
                    break;
                case 'manager':
                    $user->assignRole('Manager');
                    break;
                case 'owner':
                    $user->assignRole('Owner');
                    break;
            }
        });
    }
}
