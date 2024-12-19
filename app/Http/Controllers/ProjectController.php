<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;


class ProjectController extends Controller
{




    public function store(StoreProjectRequest $request): JsonResponse
    {
        dd($request->validated()); // عرض البيانات التي تم التحقق منها
        return response()->json(['message' => 'Validation passed!', 'data' => $request->validated()]);
    }





}
