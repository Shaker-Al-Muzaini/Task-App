<?php

namespace App\Http\Controllers;

use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    protected ProjectService $projectService;

    public function __construct(ProjectService $projectService)
    {
        $this->projectService = $projectService;
    }

    /**
     * Get a list of projects with optional query filters.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $projects = $this->projectService->getProjects($request);

        $projectsData = ProjectResource::collection($projects);


        return response()->json(['projects' => $projectsData], 200);
    }

    /**
     * Store a new project.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        try {
            $project = $this->projectService->createProject($validatedData);
            return response()->json([
                'message' => 'تم إنشاء المشروع بنجاح!',
                'data' => new ProjectResource($project),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء المشروع.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update an existing project.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        try {
            $project = $this->projectService->updateProject($id, $validatedData);
            return response()->json([
                'message' => 'تم تعديل المشروع بنجاح!',
                'data' => new ProjectResource($project),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تعديل المشروع.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
