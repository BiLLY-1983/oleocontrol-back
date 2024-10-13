<?php

namespace App\Http\Controllers;

use App\Http\Requests\OilRequest;
use App\Http\Resources\OilResource;
use App\Models\Oil;
use Illuminate\Http\JsonResponse;

class OilController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => OilResource::collection(Oil::all())
        ], 200);
    }

    public function store(OilRequest $request): JsonResponse
    {
        $oil = Oil::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new OilResource($oil)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new OilResource(Oil::findOrFail($id))
        ], 200);
    }

    public function update(OilRequest $request, $id): JsonResponse
    {
        $oil = Oil::findOrFail($id);
        $oil->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilResource($oil)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $oil = Oil::findOrFail($id);
        $oil->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
