<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => RoleResource::collection(Role::all())
        ], 200);
    }

    public function store(RoleRequest $request): JsonResponse
    {
        $role = Role::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new RoleResource($role)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new RoleResource(Role::findOrFail($id))
        ], 200);
    }

    public function update(RoleRequest $request, $id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new RoleResource($role)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
