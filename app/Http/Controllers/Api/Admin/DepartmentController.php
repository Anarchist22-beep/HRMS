<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use App\Helpers\ApiResponse;

class DepartmentController extends Controller
{
    //

    public function index(Request $request)
    {
        try {

            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $all = $request->boolean('all');

            $query = Department::query();

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            if ($all) {

                // Return all departments
                $departments = $query->where('status',1)->get();
            } else {

                // Paginated departments
                $departments = $query->paginate($perPage);
            }

            return ApiResponse::success([
                'departments' => $departments,
            ], 'departments fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                'Failed to fetch departments: ' . $e->getMessage(),
                500
            );
        }
    }

    public function store(DepartmentRequest $request)
    {
        try {
            $department = Department::create([
                'name' => $request->name,
                'description' => $request->description

            ]);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'department added successfully',
                'data' => $department
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
            $department = Department::find($id);
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'department fetched successfully',
                'data' => $department

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error fetching department: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(DepartmentRequest $request, $id)
    {
        try {
            $department = Department::find($id);
            $department->update([
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status

            ]);
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Department updated successfully',
                'data' => $department
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to update department: ' . $e->getMessage(),
                500
            );
        }
    }

    public function delete($id)
    {
        try {
            $department = Department::find($id);
            $department->delete();
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'department Deleted Successfully',

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error([
                'status' => 'error',
                'message' => 'Error deleting department' . $e->getMessage()

            ], 500);
        }
    }
}
