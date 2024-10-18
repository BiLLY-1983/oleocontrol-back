<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notification\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function indexSent($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $sentNotifications = $user->sentNotifications;

        return response()->json([
            'status' => 'success',
            'data' => NotificationResource::collection($sentNotifications)
        ], 200);
    }

    public function indexReceived($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $receivedNotifications = $user->receivedNotifications;

        return response()->json([
            'status' => 'success',
            'data' => NotificationResource::collection($receivedNotifications)
        ], 200);
    }

    public function show($userId, $notificationId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $notification = $user->receivedNotifications()->findOrFail($notificationId);

        return response()->json([
            'status' => 'success',
            'data' => new NotificationResource($notification)
        ], 200);
    }

    public function store(StoreNotificationRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $notification = Notification::create($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new NotificationResource($notification)
        ], 201);
    }
}
