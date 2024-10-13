<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberRequest; // Asegúrate de crear este Request
use App\Http\Resources\MemberResource; // Asegúrate de crear este Resource
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

    public function store(MemberRequest $request): JsonResponse
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

    public function update(MemberRequest $request, $id): JsonResponse
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
