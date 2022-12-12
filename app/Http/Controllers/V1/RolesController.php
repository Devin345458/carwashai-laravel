<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;

class RolesController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::where('company_id', getPermissionsTeamId())
            ->withCount(['users'])
            ->paginate(\request('perPage'));
        return response()->json($roles);
    }

    public function possiblePermissions(): JsonResponse
    {
        $permissions = Permission::getPossiblePermissions();
        return response()->json(compact('permissions'));
    }

    public function view(Role $role): JsonResponse
    {
        $role->load('permissions');
        $role = $role->toArray();
        $role['permissions'] = collect($role['permissions'])->pluck('name');
        return response()->json(compact('role'));
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required|array'
        ]);

        $role->name = $data['name'];
        $role->syncPermissions($data['permissions']);
        $role->save();

        return response()->json(['success' => true]);
    }

    public function create(Request $request): JsonResponse
    {
        $data = $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required|array'
        ]);

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions']);
        $role->save();


        return response()->json(['success' => true]);
    }

    public function all(): JsonResponse {
        $roles = Role::where('company_id', getPermissionsTeamId())->get();
        return response()->json(compact('roles'));
    }
}
