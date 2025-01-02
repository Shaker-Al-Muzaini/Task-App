<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TaskProgress;
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
    public function getProjects (Request $request,$slug)
    {
        $project=Project::with(['tasks.task_members.members'])
            -> where('projects.slug',$slug)->first();
        return response(['data'=>$project]);

    }
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
            // استخدام `projectService` لإنشاء المشروع مع TaskProgress
            $project = $this->projectService->createProjectWithTaskProgress($validatedData);

            return response()->json([
                'message' => 'تم إنشاء المشروع بنجاح!',
                'data' => $project,  // سيتم إرسال البيانات البسيطة فقط
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
    public function pinnedProject(Request $request)
    {
        $validatedData = $request->validate([
            'projectId' => 'required|numeric|exists:projects,id',
        ]);

        TaskProgress::where('projectId', $request->projectId)
            ->update(['pinned_on_dashboard' => TaskProgress::PINNED_ON_DASHBOARD]);

        // إرجاع رسالة نجاح
        return response()->json(['message' => 'تم تثبيت المشروع على لوحة التحكم بنجاح!']);
    }

    public function countProject()
    {
        $count = Project::count();
        return response()->json(['count'=>$count]);

    }





}
