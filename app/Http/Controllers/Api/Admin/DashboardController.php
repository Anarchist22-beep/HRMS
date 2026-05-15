<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Shift;
use App\Models\Project;
use App\Helpers\ApiResponse;

class DashboardController extends Controller
{
    //
    public function index()
    {
        try {

            $users = User::where('status', 1)
                ->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Admin');
                })
                ->count();

            $shift = Shift::where('status', 1)->count() ?? 0;

            $department = Department::where('status', 1)->count() ?? 0;
            $project = Project::where('status',1)->count() ?? 0;

            return ApiResponse::success([
                'users' => $users ?? 0,
                'shift' => $shift,
                'department' => $department,
                'project'   => $project
            ], 'Data fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                $e->getMessage(),
                500
            );
        }
    }
}
