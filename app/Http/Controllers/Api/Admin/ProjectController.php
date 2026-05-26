<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\ProjectPayment;
use App\Http\Requests\ProjecRequest;
use App\Http\Requests\UpdateProjecRequest;
use App\Http\Requests\UpdateProjectPaymentRequest;
use App\Http\Requests\ProjectStatusUpdateRequest;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
            // Create the project first
            $project = Project::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            $firstDocumentPath = null;

            // Check if document files exist
            if ($request->hasFile('document')) {
                $files = $request->file('document');
                if (!is_array($files)) {
                    $files = [$files];
                }

                // Subfolder named after the project slug
                $subfolder = Str::slug($project->name);

                foreach ($files as $index => $file) {
                    // store in: storage/app/public/projects/{subfolder}
                    $documentPath = $file->store('projects/' . $subfolder, 'public');

                    // Save in project_documents table
                    ProjectDocument::create([
                        'document' => $documentPath,
                        'project_id' => $project->id,
                    ]);

                    // Assign first document path for backward compatibility
                    if ($index === 0) {
                        $firstDocumentPath = $documentPath;
                    }
                }
            }

            if ($firstDocumentPath) {
                $project->update([
                    'document' => $firstDocumentPath,
                ]);
            }

            // Eager load documents relation
            $project->load('documents');

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'project added successfully',
                'data' => new ProjectResource($project)
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
            $project = Project::with(['documents', 'payments'])->findOrFail($id);
            return ApiResponse::success([
                'project' => new ProjectResource($project)
            ], 'project fetched successfully');
        } catch (\Exception $e) {
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

            // Update basic project info
            $project->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            // 1. Handle granular deletions of project documents
            if ($request->has('deleted_document_ids')) {
                $deletedIds = $request->input('deleted_document_ids');
                if (!is_array($deletedIds)) {
                    $deletedIds = json_decode($deletedIds, true) ?: [$deletedIds];
                }

                $documentsToDelete = ProjectDocument::whereIn('id', $deletedIds)
                    ->where('project_id', $project->id)
                    ->get();

                foreach ($documentsToDelete as $doc) {
                    if (Storage::disk('public')->exists($doc->document)) {
                        Storage::disk('public')->delete($doc->document);
                    }
                    $doc->delete();
                }
            }

            // 2. Handle uploading/appending new documents if any are selected
            if ($request->hasFile('document')) {
                $files = $request->file('document');
                if (!is_array($files)) {
                    $files = [$files];
                }

                $subfolder = Str::slug($project->name);

                foreach ($files as $file) {
                    $documentPath = $file->store('projects/' . $subfolder, 'public');

                    ProjectDocument::create([
                        'document' => $documentPath,
                        'project_id' => $project->id,
                    ]);
                }
            }

            // 3. Ensure at least one active document remains
            // Eager load documents and payments relationships
            $project->load(['documents', 'payments']);

            if ($project->documents->count() === 0) {
                return ApiResponse::error(
                    'At least one project document is required.',
                    422
                );
            }

            // 4. Keep primary project document column synchronized (always hold the path of the first document)
            $firstDoc = $project->documents->first();
            $project->update([
                'document' => $firstDoc ? $firstDoc->document : null,
            ]);

            return ApiResponse::success([
                'status' => 'success',
                'message' => 'Project updated successfully',
                'data' => new ProjectResource($project)
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
            $project = Project::findOrFail($id);

            // Physically delete all project documents from storage
            foreach ($project->documents as $doc) {
                if (Storage::disk('public')->exists($doc->document)) {
                    Storage::disk('public')->delete($doc->document);
                }
            }

            // Also delete primary document from storage if exists
            if ($project->document && Storage::disk('public')->exists($project->document)) {
                Storage::disk('public')->delete($project->document);
            }

            // Delete project record (DB cascade automatically deletes rows in project_documents)
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

    public function updatePayments($id, UpdateProjectPaymentRequest $request)
{
    try {
        $project = Project::findOrFail($id);

        // Delete all existing payments for this project
        // then re-insert fresh from the submitted rows
        ProjectPayment::where('project_id', $project->id)->delete();

        $payments = [];

        foreach ($request->input('payments') as $row) {
            $payments[] = ProjectPayment::create([
                'project_id'      => $project->id,
                'amount'          => $row['amount'],
                'amount_paid'     => $row['amount_paid'],
                'amount_received' => $row['amount_received'],
            ]);
        }

        // Reload project with all relationships
        $project->load(['documents', 'payments']);

        return ApiResponse::success([
            'status'   => 'success',
            'message'  => 'Payments updated successfully.',
            'data'     => new ProjectResource($project),
        ]);

    } catch (\Exception $e) {
        return ApiResponse::error(
            'Failed to update payments: ' . $e->getMessage(),
            500
        );
    }
}
}
