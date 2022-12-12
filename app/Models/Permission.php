<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Str;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection|Role[] $roles
 * @property-read int|null $roles_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|Permission permission($permissions)
 * @method static Builder|Permission query()
 * @method static Builder|Permission role($roles, $guard = null)
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereGuardName($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereName($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Permission extends SpatiePermission
{
    use HasFactory;

    private const POSSIBLE_PERMISSIONS = [
        'User' => [
            'Manage',
            'Remove',
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
    ];

    public static function getPossiblePermissions() {
        $possiblePermissions = [];
        foreach (Permission::POSSIBLE_PERMISSIONS as $model => $permissions) {
            $children = [];
            foreach ($permissions as $permission) {
                $children[] = [
                    'permission' => $permission . ' ' . $model,
                    'name' => $permission . ' ' . Str::headline($model) . 's'
                ];
            }
            $possiblePermissions[] = [
                'name' => Str::headline($model) . ' Permissions',
                'permission' => "All $model permission",
                'children' => $children
            ];
        }

        return $possiblePermissions;
    }
}
