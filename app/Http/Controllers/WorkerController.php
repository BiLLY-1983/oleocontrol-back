<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkerRequest; // Asegúrate de crear este Request
use App\Http\Resources\WorkerResource; // Asegúrate de crear este Resource
use App\Models\Worker;
use Illuminate\Http\JsonResponse;

class WorkerController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => WorkerResource::collection(Worker::all())
        ], 200);
    }

    public function store(WorkerRequest $request): JsonResponse
    {
        $worker = Worker::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new WorkerResource($worker)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new WorkerResource(Worker::findOrFail($id))
        ], 200);
    }

    public function update(WorkerRequest $request, $id): JsonResponse
    {
        $worker = Worker::findOrFail($id);
        $worker->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new WorkerResource($worker)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $worker = Worker::findOrFail($id);
        $worker->delete();

        return response()->json([
            'status' => 'success',
        ], 204);
    }
}
