<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Services\MemberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    protected MemberService $memberService;

    public function __construct(MemberService $memberService)
    {
        $this->memberService = $memberService;
    }


    public function index(Request $request): JsonResponse
    {
        $Member = $this->memberService->getMember($request);

        $MemberData = MemberResource::collection($Member);


        return response()->json(['Member' => $MemberData], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email ',
        ]);

        try {
            $member = $this->memberService->createMember($validatedData);
            return response()->json([
                'message' => 'تم إنشاء العضو بنجاح!',
                'data' => $member,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء إنشاء العضو.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        //
    }


    public function update(Request $request,$id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|',
        ]);


        try {
            $member = $this->memberService->updateMember($id, $validatedData);

            return response()->json([
                'message' => 'تم تعديل العضو بنجاح!',
                'data' => new MemberResource($member),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'لم يتم العثور على العضو.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'حدث خطأ أثناء تعديل العضو.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        //
    }
}
