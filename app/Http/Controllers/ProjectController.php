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
     * تخزين مشروع جديد.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        // التحقق من صحة البيانات الواردة
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'startDate' => 'required|date',
                'endDate' => 'required|date|after_or_equal:startDate',
            ]);
        } catch (ValidationException $e) {
            // إرجاع الأخطاء إذا فشل التحقق
            return response()->json(['errors' => $e->errors()], 422);
        }

        // بدء المعاملة
        DB::beginTransaction();

        try {
            // إنشاء المشروع باستخدام البيانات التي تم التحقق منها
            $project = Project::create([
                'name' => $validatedData['name'],
                'status' => Project::NOT_STARTED,
                'startDate' => $validatedData['startDate'],
                'endDate' => $validatedData['endDate'],
                'slug' => Project::createSlug($validatedData['name']),
            ]);

            // تأكيد المعاملة
            DB::commit();

            // تحويل المشروع إلى مصفوفة باستخدام Resource
            $projectData = (new ProjectResource($project))->toArray($request);

            // دمج البيانات مع الرسالة في الجذر
            return response()->json(
                array_merge(['message' => 'تم إنشاء المشروع بنجاح!'], $projectData),
                201
            );
        } catch (\Exception $e) {
            // التراجع عن المعاملة إذا حدث خطأ
            DB::rollBack();

            // إرجاع استجابة خطأ
            return response()->json(
                ['message' => 'حدث خطأ أثناء إنشاء المشروع.', 'error' => $e->getMessage()],
                500
            );
        }
    }
}
