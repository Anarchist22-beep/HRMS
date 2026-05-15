<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Helpers\ApiResponse;
use App\Models\User;
use App\Models\Department;
use App\Models\Project;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserStatusUpdateRequest;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    //

    public function index(Request $request)
    {
        try {

            $perPage = $request->get('per_page', 10);
            $search  = $request->get('search');

            $query = User::with([
                'departments:id,name',   //  only id + name
            ]);

            // Search
            if ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            }

            // Exclude Admin users
            $query->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Admin');
            });

            // Exclude logged-in user
            if (auth()->check()) {
                $query->where('id', '!=', auth()->id());
            }

            $users = $query->paginate($perPage);

            return ApiResponse::success([
                'users' => $users,
            ], 'users fetched successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch users: ' . $e->getMessage(), 500);
        }
    }

    public function create()
    {
        try {
            $user = auth()->user();

            // Get roles
            if ($user->hasRole('Admin')) {
                $roles = Role::get(); // Admin sees all roles
            } else {
                $roles = Role::where('name', '!=', 'Admin')->get(); // hide Admin role
            }

            $departments = Department::where('status', 1)->get();
            $projects = Project::where('status', 1)->get();

            return ApiResponse::success([
                'roles'       => $roles,
                'departments' => $departments,
                'projects'    => $projects,
            ], 'Data fetched successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to fetch data: ' . $e->getMessage(),
                500
            );
        }
    }

    public function store(UserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'role' => $request->role,
                'designation' => $request->designation,
                'salary' => $request->salary,
                'depart_id' => $request->depart_id,
                'project_id' => $request->project_id,
            ]);

            if ($request->role) {
                $user->assignRole($request->role);
            }

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to create user: ' . $e->getMessage(),
                500
            );
        }
    }

    public function edit($id)
    {
        try {
            $loggedInUser = auth()->user();

            $user = User::with('roles')->findOrFail($id);

            // Roles (hide Admin if not admin)
            $rolesQuery = Role::query();

            if (!$loggedInUser->hasRole('Admin')) {
                $rolesQuery->where('name', '!=', 'Admin');
            }

            $roles = $rolesQuery->get();

            $departments = Department::where('status', 1)->get();
            $projects = Project::where('status', 1)->get();

            return ApiResponse::success([
                'user'        => $user,
                'roles'       => $roles,
                'departments' => $departments,
                'projects'    => $projects,
            ], 'Data fetched successfully');
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to fetch user: ' . $e->getMessage(),
                500
            );
        }
    }

    public function update($id, UserUpdateRequest $request)
    {
        try {
            $user = User::findOrFail($id);

            $data = [
                'name'        => $request->name,
                'email'       => $request->email,
                'designation' => $request->designation,
                'salary'      => $request->salary,
                'depart_id'   => $request->depart_id,
                'project_id'  => $request->project_id,
                'role' => $request->role,
            ];

            //  Hash password only if provided
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            //  Sync Spatie role
            if ($request->role) {
                $user->syncRoles([$request->role]);
            }

            return ApiResponse::success([
                'message' => 'User updated successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to update user: ' . $e->getMessage(),
                500
            );
        }
    }

    public function updateStatus($id,UserStatusUpdateRequest $request){
        try{
            $user = User::findorfail($id);
            $user->update([
                'status'=>$request->status

            ]);

            return ApiResponse::success([
                'message'=>'User status updated successfully',
                'data'=>$user
            ]);

        }catch(\Exception $e){
             return ApiResponse::error(
                'Failed to update user status: ',
                500
            );

        }
    }

    public function delete($id)
    {
        try {
            $user = User::find($id);

            $user->delete();
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'User Deleted Successfully',

            ]);
        } catch (\Exception $e) {
            //  dd($e);
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error deleting user' . $e->getMessage()

            ], 500);
        }
    }
}
