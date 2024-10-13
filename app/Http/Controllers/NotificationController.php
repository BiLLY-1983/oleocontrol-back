<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => NotificationResource::collection(Notification::all())
        ], 200);
    }

    public function store(NotificationRequest $request): JsonResponse
    {
        $notification = Notification::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new NotificationResource($notification)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new NotificationResource(Notification::findOrFail($id))
        ], 200);
    }

    public function update(NotificationRequest $request, $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new NotificationResource($notification)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }
}
