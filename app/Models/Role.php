<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property int|null $store_id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Role create(array $attributes)
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role permission($permissions)
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereGuardName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereStoreId($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int|null $company_id
 * @method static Builder|Role whereCompanyId($value)
 */
class Role extends SpatieRole
{
    use HasFactory;

    public const DEFAULT_ROLES = [
        [
            'name' => 'User',
            'permissions' => [
                'Repair' => [
                    'Manage',
                    'View',
                ],
                'IncidentFormSubmission' => [
                    'Submit',
                ],
                'Maintenance' => [
                    'Conduct',
                ],
                'Procedure' => [
                    'Conduct',
                ],
                'Inventory' => [
                    'View',
                    'Conduct'
                ],
                'Equipment' => [
                    'View'
                ],
            ]
        ],
        [
            'name' => 'Manager',
            'permissions' => [
                'User' => [
                    'Manage',
                    'Delete',
                ],
                'Store' => [ // Store/Warehouses
                    'Manage',
                ],
                'Repair' => [
                    'Manage',
                    'View',
                    'Delete'
                ],
                'IncidentForm' => [
                    'Manage',
                ],
                'IncidentFormSubmission' => [
                    'Submit',
                    'Manage',
                    'Delete',
                ],
                'Maintenance' => [
                    'Manage',
                    'Conduct',
                    'Delete'
                ],
                'Procedure' => [
                    'Manage',
                    'Assign',
                    'Conduct',
                    'Delete',
                ],
                'Inventory' => [
                    'Manage',
                    'View',
                    'Delete',
                    'Conduct'
                ],
                'Equipment' => [
                    'Manage',
                    'Delete',
                    'View'
                ],
                'Location' => [
                    'Manage',
                    'Delete',
                ],
                'Supplier' => [
                    'Manage',
                    'Delete'
                ],
                'Order' => [
                    'Manage',
                    'Delete',
                ],
                'TransferRequest' => [
                    'Manage',
                    'Delete',
                ],
            ]
        ],
        [
            'name' => 'Owner',
            'permissions' => [
                'User' => [
                    'Manage',
                    'Delete',
                ],
                'Company' => [
                    'Details',
                    'Subscriptions',
                    'Users',
                    'Roles'
                ],
                'Store' => [ // Store/Warehouses
                    'Manage',
                ],
                'ItemType' => [
                    'Manage',
                    'Delete',
                ],
                'Repair' => [
                    'Manage',
                    'View',
                    'Delete'
                ],
                'IncidentForm' => [
                    'Manage',
                ],
                'IncidentFormSubmission' => [
                    'Submit',
                    'Manage',
                    'Delete',
                ],
                'Maintenance' => [
                    'Manage',
                    'Conduct',
                    'Delete'
                ],
                'Procedure' => [
                    'Manage',
                    'Assign',
                    'Conduct',
                    'Delete',
                ],
                'Inventory' => [
                    'Manage',
                    'View',
                    'Delete',
                    'Conduct'
                ],
                'Equipment' => [
                    'Manage',
                    'Delete',
                    'View'
                ],
                'Location' => [
                    'Manage',
                    'Delete',
                ],
                'Supplier' => [
                    'Manage',
                    'Delete',
                ],
                'Order' => [
                    'Manage',
                    'Delete',
                ],
                'TransferRequest' => [
                    'Manage',
                    'Delete',
                ],
            ]
        ]
    ];


    public static function addDefaultRoles(int $companyId) {
        setPermissionsTeamId($companyId);

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
    }
}
