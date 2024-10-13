<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => DepartmentResource::collection(Department::all())
        ], 200);
    }

    public function store(DepartmentRequest $request): JsonResponse
    {
        $department = Department::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new DepartmentResource($department)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new DepartmentResource(Department::findOrFail($id))
        ], 200);
    }

    public function update(DepartmentRequest $request, $id): JsonResponse
    {
        $department = Department::findOrFail($id);
        $department->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new DepartmentResource($department)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
