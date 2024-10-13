<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettlementRequest;
use App\Http\Resources\SettlementResource;
use App\Models\Settlement;
use Illuminate\Http\JsonResponse;

class SettlementController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => SettlementResource::collection(Settlement::all())
        ], 200);
    }

    public function store(SettlementRequest $request): JsonResponse
    {
        $settlement = Settlement::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource(Settlement::findOrFail($id))
        ], 200);
    }

    public function update(SettlementRequest $request, $id): JsonResponse
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
}
