<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settlement\StoreSettlementRequest;
use App\Http\Requests\Settlement\UpdateSettlementRequest;
use App\Http\Resources\SettlementResource;
use App\Models\Employee;
use App\Models\Member;
use App\Models\Settlement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SettlementController extends Controller
{
    /**
     *   Función que muestra las liquidaciones de aceite.
     * 
     *   @return JsonResponse Respuesta JSON con un mensaje de éxito
     *   y una colección con todos las liquidaciones de aceite de la aplicación.
     */
    public function index(): JsonResponse
    {
        $settlements = Settlement::all();

        return response()->json([
            'status' => 'success',
            'data' => SettlementResource::collection($settlements)
        ], 200);
    }

    public function store(StoreSettlementRequest $request): JsonResponse
    {
        $settlement = Settlement::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 201);
    }

    /**
    *   Función para mostrar una liquidación en concreto.
    *
    *   @param int ID de la liquidación a mostrar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos de la liquidación.
    */
    public function show($id): JsonResponse
    {
        $settlement = Settlement::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }

    public function update(UpdateSettlementRequest $request, $id): JsonResponse
    {
        $settlement = Settlement::findOrFail($id);
        $settlement->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $settlement = Settlement::findOrFail($id);
        $settlement->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function indexForEmployee($employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $settlements = $employee->settlements;
        
        return response()->json([
            'status' => 'success',
            'data' => SettlementResource::collection($settlements)
        ], 200);
    }

    public function showForEmployee($employeeId, $settlementId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $settlement = $employee->settlements()->findOrFail($settlementId);

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }

    public function updateForEmployee(UpdateSettlementRequest $request, $employeeId, $settlementId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $settlement = $employee->settlements()->findOrFail($settlementId);

        $settlement->update($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }

    public function indexForMember($memberId): JsonResponse
    {
        $member = Member::findOrFail($memberId);
        $settlements = $member->settlements;

        return response()->json([
            'status' => 'success',
            'data' => SettlementResource::collection($settlements)
        ], 200);
    }

    public function showForMember($memberId, $settlementId): JsonResponse
    {
        $member = Member::findOrFail($memberId);
        $settlement = $member->settlements()->findOrFail($settlementId);

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }
}
