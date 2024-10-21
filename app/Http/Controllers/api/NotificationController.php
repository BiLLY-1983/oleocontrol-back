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
    /**
     * Muestra las notificaciones enviadas por un usuario.
     * 
     * Este método recibe un ID de usuario, busca las notificaciones enviadas por el usuario en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de las notificaciones.
     *
     * @param int $userId El ID del usuario.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de las notificaciones enviadas.
     */
    public function indexSent($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $sentNotifications = $user->sentNotifications;

        return response()->json([
            'status' => 'success',
            'data' => NotificationResource::collection($sentNotifications)
        ], 200);
    }

    /**
     * Muestra las notificaciones recibidas por un usuario.
     * 
     * Este método recibe un ID de usuario, busca las notificaciones recibidas por el usuario en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de las notificaciones.
     *
     * @param int $userId El ID del usuario.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de las notificaciones recibidas.
     */
    public function indexReceived($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $receivedNotifications = $user->receivedNotifications;

        return response()->json([
            'status' => 'success',
            'data' => NotificationResource::collection($receivedNotifications)
        ], 200);
    }

    /**
     * Muestra una notificación específica por su ID.
     * 
     * Este método recibe un ID de usuario y un ID de notificación, busca la notificación asociada al usuario en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de la notificación.
     *
     * @param int $userId El ID del usuario.
     * @param int $notificationId El ID de la notificación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la notificación.
     */
    public function show($userId, $notificationId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $notification = $user->receivedNotifications()->findOrFail($notificationId);

        return response()->json([
            'status' => 'success',
            'data' => new NotificationResource($notification)
        ], 200);
    }

    /**
     * Crea una nueva notificación.
     * 
     * Este método recibe una solicitud de creación de notificación, valida los datos y crea una nueva notificación en la base de datos.
     * La respuesta incluye un estado de éxito y los datos de la notificación creada en formato JSON.
     *
     * @param StoreNotificationRequest $request La solicitud de creación de notificación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la notificación creada.
     */
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
