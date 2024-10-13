<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnalysisRequest;
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

    public function store(AnalysisRequest $request): JsonResponse
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

    public function update(AnalysisRequest $request, $id): JsonResponse
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
