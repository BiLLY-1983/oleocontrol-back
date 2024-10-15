<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analysis\StoreAnalysisRequest;
use App\Http\Requests\Analysis\UpdateAnalysisRequest;
use App\Http\Resources\AnalysisResource;
use App\Models\Analysis;
use Illuminate\Http\JsonResponse;

class AnalysisController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => AnalysisResource::collection(Analysis::all())
        ], 200);
    }

    public function store(StoreAnalysisRequest $request): JsonResponse
    {
        $analysis = Analysis::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource(Analysis::findOrFail($id))
        ], 200);
    }

    public function update(UpdateAnalysisRequest $request, $id): JsonResponse
    {
        $analysis = Analysis::findOrFail($id);
        $analysis->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $analysis = Analysis::findOrFail($id);
        $analysis->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
