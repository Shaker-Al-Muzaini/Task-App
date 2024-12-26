<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class MemberService
{

    public function getMember($request)
    {
        return Member::with('task_progress')
            ->when($request->filled('query'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->get('query') . '%');
            })
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function createMember(array $data): object
    {
        DB::beginTransaction();

        try {
            $MemberData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $MemberId = DB::table('members')->insertGetId($MemberData);

            DB::commit();

            return (object)[
                'id' => $MemberId,
                'name' => $data['name'],
                'email' => $data['email'],

            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateMember(int $id, array $validatedData): Member
    {
        DB::beginTransaction();

        try {
            $member = Member::findOrFail($id);
            $member->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
            ]);

            DB::commit();

            return $member;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }



}
