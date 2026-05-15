<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use App\Helpers\ApiResponse;



class PermissionController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $all = $request->boolean('all'); //  safer than get()

            $query = Permission::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            if ($all) {
                //  return all (for dropdowns / role assignment)
                $permissions = $query->get()->map(function ($permission) {
                    $permission->display_name = ucwords(str_replace('_', ' ', $permission->name));
                    return $permission;
                });
            } else {
                //  default: paginated
                $permissions = $query->paginate($perPage);

                $permissions->getCollection()->transform(function ($permission) {
                    $permission->display_name = ucwords(str_replace('_', ' ', $permission->name));
                    return $permission;
                });
            }

            return ApiResponse::success([
                'permissions' => $permissions,
            ], 'Permission Fetched Successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch permissions:' . $e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',

            ]);

            $permission = Permission::create([
                'name' => $request->name,
                'guard_name' => 'api'

            ]);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Permission created successfully',
                'data' => $permission

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error creating permission'

            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $permission = Permission::find($id);
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Permission fetched successfully',
                'data' => $permission,
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error fetching permission'
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $permission = Permission::find($id);
            //dd($permission);
            $request->validate([
                'name' => 'required'
            ]);

            $permission->update([
                'name' => $request->name,
                'guard_name' => 'api'
            ]);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Permission updated successfully',
                'data' => $permission

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'status'  => 'error',
                'message' => 'Error updating permission: ' . $e->getMessage() // Temporarily add this for debugging
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $permission = Permission::find($id);

            $permission->delete();
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Permission Deleted Successfully',

            ]);
        } catch (\Exception $e) {
            dd($e);
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error deleting permission' . $e->getMessage()

            ], 500);
        }
    }
}
