<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => MemberResource::collection(Member::all())
        ], 200);
    }

    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = Member::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new MemberResource(Member::findOrFail($id))
        ], 200);
    }

    public function update(UpdateMemberRequest $request, $id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $member->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return response()->json([
            'status' => 'success',
        ], 204);
    }
}
