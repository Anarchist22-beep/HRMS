<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Helpers\ApiResponse;
use App\Http\Requests\RoleRequest;



class RoleController extends Controller
{
    //

    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $query = Role::query();
            if ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            }

            $roles = $query->paginate($perPage);

            return ApiResponse::success([
                'roles' => $roles,
            ], 'roles fetched successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch roles:' . $e->getMessage(), 500);
        }
    }

    public function store(RoleRequest $request)
    {
        try {

            // 1. Create Role
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'api'
            ]);

            // 2. Attach permissions (Spatie way)
            $role->syncPermissions($request->permissions);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Role created successfully',
                'data' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Failed to create role: ' . $e->getMessage(),
                500
            );
        }
    }

    public function edit($id)
    {
        try {

            $role = Role::with('permissions')->findOrFail($id);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Role fetched successfully',
                'data' => $role
            ]);
        } catch (\Exception $e) {

            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error fetching role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(RoleRequest $request, $id)
    {
        try {

            // 1. Find role
            $role = Role::findOrFail($id);

            // 2. Update role name + guard
            $role->update([
                'name' => $request->name,
                'guard_name' => 'api'
            ]);

            // 3. Sync permissions (replace old with new)
            $role->syncPermissions($request->permissions);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Role updated successfully',
                'data' => $role->load('permissions')
            ]);
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Failed to update role: ' . $e->getMessage(),
                500
            );
        }
    }

    public function delete($id)
    {
        try {
            $role = Role::find($id);

            $role->delete();
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Role Deleted Successfully',

            ]);
        } catch (\Exception $e) {
            //  dd($e);
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error deleting role' . $e->getMessage()

            ], 500);
        }
    }
}
