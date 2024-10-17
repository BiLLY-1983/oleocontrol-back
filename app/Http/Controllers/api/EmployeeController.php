<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateOEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EmployeeResource::collection(Employee::all())
        ], 200);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = Employee::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new EmployeResource(Employee::findOrFail($id))
        ], 200);
    }

    public function update(UpdateEmployeeRequest $request, $id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EmployeeResource($employee)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json([
            'status' => 'success',
        ], 204);
    }
}
