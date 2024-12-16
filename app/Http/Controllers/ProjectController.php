<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Store a new project.
     *
     * @param StoreProjectRequest $request
     * @return JsonResponse
     */


    public function store(StoreProjectRequest $request): JsonResponse
    {
        DB::beginTransaction();  // Start a new transaction

        try {
            // Create the project with validated data
            $project = Project::create([
                'name' => $request->validated('name'),
                'status' => Project::NOT_STARTED,
                'startDate' => $request->validated('startDate'),
                'endDate' => $request->validated('endDate'),
                'slug' => Project::createSlug($request->validated('name')),
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Project created successfully.',
                'project' => $project,
            ], 201);
        } catch (\Exception $e) {
            // معالجة الأخطاء العامة
            Log::error('Project creation failed: ' . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'message' => config('app.debug') ? $e->getMessage() : 'An error occurred while creating the project.',
            ], 500);
        }
    }

}
