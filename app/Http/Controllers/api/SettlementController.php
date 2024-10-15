<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settlement\StoreSettlementRequest;
use App\Http\Requests\Settlement\UpdateOSettlementRequest;
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

    public function store(StoreSettlementRequest $request): JsonResponse
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

    public function update(UpdateOSettlementRequest $request, $id): JsonResponse
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
