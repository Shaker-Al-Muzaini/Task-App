<?php

namespace App\Services;

use App\Models\Project;
use App\Models\TaskProgress;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    /**
     * Get a paginated list of projects with optional filtering.
     *
     * @param $request
     * @return mixed
     */
    public function getProjects($request)
    {
        return Project::with('task_progress')
            ->when($request->filled('query'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('query') . '%');
            })
            ->orderByDesc('id')
            ->paginate(10);
    }

    /**
     * Create a new project and its associated task progress.
     *
     * @param array $data
     * @return Project
     * @throws \Exception
     */

    public function createProjectWithTaskProgress(array $data): object
    {
        DB::beginTransaction();

        try {
            // إدخال البيانات باستخدام DB::table لتقليل استهلاك الذاكرة
            $projectData = [
                'name' => $data['name'],
                'status' => Project::NOT_STARTED,
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
                'slug' => Project::createSlug($data['name']),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $projectId = DB::table('projects')->insertGetId($projectData);

            // إضافة بيانات task_progress باستخدام DB::table لتقليل استهلاك الذاكرة
            DB::table('task_progress')->insert([
                'projectId' => $projectId,
                'pinned_on_dashboard' => TaskProgress::NOT_PINNED_ON_DASHBOARD,
                'progress' => TaskProgress::INITAL_PROJECT_PERCENT,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // استرجاع المشروع مع إعطاء التفاصيل الأساسية فقط
            return (object)[
                'id' => $projectId,
                'name' => $data['name'],
                'slug' => Project::createSlug($data['name']),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Update an existing project.
     *
     * @param int $id
     * @param array $data
     * @return Project
     * @throws \Exception
     */
    public function updateProject(int $id, array $data): Project
    {
        DB::beginTransaction();

        try {
            $project = Project::findOrFail($id);

            $project->update([
                'name' => $data['name'],
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
                'slug' => Project::createSlug($data['name']),
            ]);

            DB::commit();

            return $project;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
