<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Project;
use App\Http\Requests\ProjecRequest;
use App\Http\Requests\UpdateProjecRequest;
use App\Http\Requests\ProjectStatusUpdateRequest;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Storage;


class ProjectController extends Controller
{
    //

    public function index(Request $request)
    {
        try {

            $perPage = $request->get('per_page', 10);
            $search  = $request->get('search');

            $query = Project::select(
                'id',
                'name',
                'status',
                'description'
            );

            // Search
            if ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            }

            // Descending order
            $query->orderBy('id', 'desc');

            $projects = $query->paginate($perPage);

            return ApiResponse::success([
                'projects' => $projects,
            ], 'projects fetched successfully');
        } catch (\Exception $e) {
            return ApiResponse::error('Failed to fetch projects: ' . $e->getMessage(), 500);
        }
    }

    public function store(ProjecRequest $request)
    {
        try {
            $documentPath = null;

            // check if file exists
            if ($request->hasFile('document')) {
                $file = $request->file('document');

                // store in: storage/app/public/projects
                $documentPath = $file->store('projects', 'public');
            }
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
                'document' => $documentPath,

            ]);
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'project added successfully',
                'data' => $project

            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to create project: ' . $e->getMessage(),
                500
            );
        }
    }

    public function edit($id)
    {
        try {
            $project = Project::find($id);
            return ApiResponse::success([
                'project' => new ProjectResource($project)

            ], 'project fetched successfully');
        } catch (\exception $e) {
            return ApiResponse::error(
                'Failed to fetch project: ' . $e->getMessage(),
                500
            );
        }
    }

    public function update($id, UpdateProjecRequest $request)
    {
        try {

            $project = Project::findOrFail($id);

            $documentPath = $project->document; // keep old file by default

            // if new file uploaded → replace
            if ($request->hasFile('document')) {

                // delete old file if exists
                if ($project->document && Storage::disk('public')->exists($project->document)) {
                    Storage::disk('public')->delete($project->document);
                }

                // store new file
                $file = $request->file('document');
                $documentPath = $file->store('projects', 'public');
            }

            // update project
            $project->update([
                'name' => $request->name,
                'description' => $request->description,
                'document' => $documentPath,
            ]);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Project updated successfully',
                'data' => $project
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to update project: ' . $e->getMessage(),
                500
            );
        }
    }

    public function delete($id)
    {
        try {
            $project = Project::find($id);
            if ($project->document && Storage::disk('public')->exists($project->document)) {
                Storage::disk('public')->delete($project->document);
            }

            // delete record
            $project->delete();
            return ApiResponse::success([
                'status' => 'success',
                'message' => 'project deleted successfully'
            ]);
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to delete project: ' . $e->getMessage(),
                500
            );
        }
    }


    public function updateStatus($id,ProjectStatusUpdateRequest $request){
        try{
            $project = Project::findorfail($id);
            $project->update([
                'status'=>$request->status

            ]);

            return ApiResponse::success([
                'message'=>'Project status updated successfully',
                'data'=>$project
            ]);

        }catch(\Exception $e){
             return ApiResponse::error(
                'Failed to update project status: ',
                500
            );

        }
    }
}
