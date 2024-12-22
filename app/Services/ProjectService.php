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
    public function createProject(array $data): Project
    {
        DB::beginTransaction();

        try {
            $project = Project::create([
                'name' => $data['name'],
                'status' => Project::NOT_STARTED,
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
                'slug' => Project::createSlug($data['name']),
            ]);

            TaskProgress::create([
                'projectId' => $project->id,
                'pinned_on_dashboard' => TaskProgress::NOT_PINNED_ON_DASHBOARD,
                'progress' => TaskProgress::INITAL_PROJECT_PERCENT,
            ]);

            DB::commit();

            return $project;
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
