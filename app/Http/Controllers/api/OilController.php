<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Oil\StoreOilRequest;
use App\Http\Requests\Oil\UpdateOilRequest;
use App\Http\Resources\OilResource;
use App\Models\Oil;
use Illuminate\Http\JsonResponse;

class OilController extends Controller
{
    /**
     * Muestra todos los aceites.
     * 
     * Este método obtiene todos los aceites de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los aceites.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los aceites.
     * 
     * @OA\Get(
     *     path="/api/oils",
     *     summary="Obtener todos los aceites",
     *     description="Devuelve una lista de todos los aceites registrados en el sistema.",
     *     operationId="getOils",
     *     tags={"Aceites"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de aceites",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/OilResource")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => OilResource::collection(Oil::all())
        ], 200);
    }

    /**
     * Crea un nuevo aceite.
     * 
     * Este método recibe una solicitud de creación de aceite, valida los datos y crea un nuevo aceite en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del aceite creado en formato JSON.
     *
     * @param StoreOilRequest $request La solicitud de creación de aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del aceite creado.
     * 
     * @OA\Post(
     *     path="/api/oils",
     *     summary="Crear un nuevo aceite",
     *     description="Crea un nuevo aceite en el sistema.",
     *     operationId="createOil",
     *     tags={"Aceites"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "price"},
     *             @OA\Property(property="name", type="string", example="Aceite de Oliva"),
     *             @OA\Property(property="description", type="string", example="Aceite de oliva virgen extra."),
     *             @OA\Property(property="price", type="number", format="float", example=10.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Aceite creado satisfactoriamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/OilResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error de validación."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor.")
     *         )
     *     )
     * )
     */
    public function store(StoreOilRequest $request): JsonResponse
    {
        $oil = Oil::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilResource($oil)
        ], 201);
    }

    /**
     * Muestra un aceite específico por su ID.
     * 
     * Este método recibe un ID de aceite, busca el aceite en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del aceite.
     *
     * @param int $oilId El ID del aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del aceite.
     * 
     * @OA\Get(
     *     path="/api/oils/{oilId}",
     *     summary="Obtener un aceite por ID",
     *     description="Devuelve los detalles de un aceite específico por su ID.",
     *     operationId="getOilById",
     *     tags={"Aceites"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="oilId",
     *         in="path",
     *         required=true,
     *         description="ID del aceite",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aceite encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/OilResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ), 
     *     @OA\Response(
     *         response=404,
     *         description="Aceite no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Aceite no encontrado.")
     *         )
     *     )
     * )
     */
    public function show($oilId): JsonResponse
    {
        $oil = Oil::findOrFail($oilId);

        return response()->json([
            'status' => 'success',
            'data' => new OilResource($oil)
        ], 200);
    }

    /**
     * Actualiza un aceite específico por su ID.
     * 
     * Este método recibe una solicitud de actualización de aceite, valida los datos y actualiza el aceite en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del aceite actualizado en formato JSON.
     *
     * @param UpdateOilRequest $request La solicitud de actualización de aceite.
     * @param int $id El ID del aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del aceite actualizado.
     * 
     * @OA\Put(
     *     path="/api/oils/{id}",
     *     summary="Actualizar un aceite",
     *     description="Actualiza los datos de un aceite existente.",
     *     operationId="updateOil",
     *     tags={"Aceites"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del aceite",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "price"},
     *             @OA\Property(property="name", type="string", example="Aceite de Oliva"),
     *             @OA\Property(property="description", type="string", example="Aceite de oliva virgen extra."),
     *             @OA\Property(property="price", type="number", format="float", example=10.99)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aceite actualizado satisfactoriamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/OilResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aceite no encontrado",
     *         @OA\JsonContent( 
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Aceite no encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ), 
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor.")
     *         )
     *     )
     * )
     */
    public function update(UpdateOilRequest $request, $id): JsonResponse
    {
        $oil = Oil::findOrFail($id);
        $oil->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilResource($oil)
        ], 200);
    }

    /**
     * Elimina un aceite específico por su ID.
     * 
     * Este método recibe un ID de aceite, busca el aceite en la base de datos y elimina el aceite.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     * 
     * @OA\Delete(
     *     path="/api/oils/{id}",
     *     summary="Eliminar un aceite",
     *     description="Elimina un aceite existente por su ID.",
     *     operationId="deleteOil",
     *     tags={"Aceites"},
     *     security={{
     *         "bearerAuth": {}
     *     }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del aceite a eliminar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Aceite eliminado satisfactoriamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Aceite eliminado satisfactoriamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Aceite no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Aceite no encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor.")
     *         )
     *     )
     * )
     */
    public function destroy($id): JsonResponse
    {
        $oil = Oil::findOrFail($id);
        $oil->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Aceite eliminado satisfactoriamente.'
        ], 200);
    }
}
