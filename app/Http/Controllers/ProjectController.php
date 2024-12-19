<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**

     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // بدء المعاملة
        DB::beginTransaction();

        try {
            $project = Project::create([
                'name' => $validatedData['name'],
                'status' => Project::NOT_STARTED,
                'startDate' => $validatedData['startDate'],
                'endDate' => $validatedData['endDate'],
                'slug' => Project::createSlug($validatedData['name']),
            ]);

            DB::commit();

            // تحويل المشروع إلى مصفوفة باستخدام Resource
            $projectData = (new ProjectResource($project))->toArray($request);

            return response()->json(
                array_merge(['message' => 'تم إنشاء المشروع بنجاح!'], $projectData),
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(
                ['message' => 'حدث خطأ أثناء إنشاء المشروع.', 'error' => $e->getMessage()],
                500
            );
        }
    }
}
