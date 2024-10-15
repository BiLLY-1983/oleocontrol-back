<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entry\StoreEntryRequest;
use App\Http\Requests\Entry\UpdateEntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use Illuminate\Http\JsonResponse;

class EntryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EntryResource::collection(Entry::all())
        ], 200);
    }

    public function store(StoreEntryRequest $request): JsonResponse
    {
        $entry = Entry::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new EntryResource(Entry::findOrFail($id))
        ], 200);
    }

    public function update(UpdateEntryRequest $request, $id): JsonResponse
    {
        $entry = Entry::findOrFail($id);
        $entry->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $entry = Entry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
