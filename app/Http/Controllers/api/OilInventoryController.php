<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OilInventory\StoreOilInventoryRequest;
use App\Http\Resources\OilInventoryResource;
use App\Models\Member;
use App\Models\OilInventory;
use Illuminate\Http\JsonResponse;

class OilInventoryController extends Controller
{
    /**
     * Muestra un listado de todos los inventarios de aceite.
     * 
     * Este método recupera todos los registros de inventario de aceite y los devuelve en formato JSON.
     *
     * @return JsonResponse Respuesta JSON con el listado de inventarios de aceite.
     * 
     * @OA\Get(
     *     path="/api/oil-inventories",
     *     summary="Listar todos los inventarios de aceite",
     *     tags={"Inventarios de Aceite"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de inventarios de aceite",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/OilInventoryResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Prohibido",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Prohibido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $inventories = OilInventory::all();

        return response()->json([
            'status' => 'success',
            'data' => OilInventoryResource::collection($inventories),
        ]);
    }

    /**
     * Muestra los detalles de un inventario de aceite específico.
     * 
     * Este método devuelve la información detallada del inventario de aceite solicitado.
     *
     * @param OilInventory $oilInventory El inventario de aceite a mostrar.
     * @return JsonResponse Respuesta JSON con los datos del inventario de aceite.
     * 
     * @OA\Get(
     *     path="/api/oil-inventories/{id}",
     *     summary="Mostrar un inventario de aceite específico",
     *     tags={"Inventarios de Aceite"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del inventario de aceite",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del inventario de aceite",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/OilInventoryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Prohibido",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Prohibido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor")
     *         )
     *     )
     * )
     */
    public function show(OilInventory $oilInventory): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new OilInventoryResource($oilInventory),
        ]);
    }

    /**
     * Crea un nuevo inventario de aceite.
     * 
     * Este método recibe una solicitud de creación de inventario de aceite, valida los datos y crea un nuevo registro en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del inventario de aceite creado en formato JSON.
     *
     * @param StoreOilInventoryRequest $request La solicitud de creación de inventario de aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del inventario de aceite creado.
     * 
     * @OA\Post(
     *     path="/api/oil-inventories",
     *     summary="Crear un nuevo inventario de aceite",
     *     tags={"Inventarios de Aceite"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="member_id", type="integer", example=1),
     *             @OA\Property(property="oil_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=100),
     *             @OA\Property(property="date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="description", type="string", example="Inventario inicial")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Inventario de aceite creado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/OilInventoryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Solicitud incorrecta")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Prohibido",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Prohibido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor")
     *         )
     *     )
     * )
     */
    public function store(StoreOilInventoryRequest $request): JsonResponse
    {
        $oilInventory = OilInventory::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilInventoryResource($oilInventory)
        ], 201);
    }


    /**
     * Muestra todos los inventarios de aceite asociados a un socio específico y calcula el total de la cantidad por tipo de aceite.
     * 
     * Este método busca el socio por su ID, devuelve todos sus inventarios de aceite y calcula el total de la cantidad por tipo de aceite.
     * Se utiliza el recurso OilInventoryResource para formatear la respuesta.
     *
     * @param int $memberId ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito, los inventarios de aceite y el total por tipo de aceite.
     * 
     * @OA\Get(
     *     path="/api/members/{memberId}/oil-inventories",
     *     summary="Listar inventarios de aceite por socio",
     *     tags={"Inventarios de Aceite"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del socio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Listado de inventarios de aceite por socio",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="oil_id", type="integer", example=1),
     *                 @OA\Property(property="oil_name", type="string", example="Aceite de Oliva"),
     *                 @OA\Property(property="total_quantity", type="integer", example=100)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Prohibido",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Prohibido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor")
     *         )
     *     )
     * )
     */
    public function indexForMember($memberId): JsonResponse
    {
        // Cargar el miembro y la relación 'oil_inventories'
        $member = Member::findOrFail($memberId);
        $oil_inventories = $member->oilInventories;

        // Verificar si la colección está vacía
        if ($oil_inventories->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => [],  // Devuelve un array vacío
            ], 200);
        }

        // Agrupar las liquidaciones por tipo de aceite
        $groupedByOil = $oil_inventories->groupBy('oil_id');

        // Sumar las cantidades por tipo de aceite
        $totalByOil = $groupedByOil->map(function ($inventories) {
            // Asegúrate de que existe un aceite asociado
            $firstInventory = $inventories->first();
            $oil = $firstInventory->oil;
        
            return [
                'oil_id' => $inventories->first()->oil_id,
                'oil_name' => $oil ? $oil->name : 'Desconocido',
                'total_quantity' => $inventories->sum('quantity'), 
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $totalByOil->values()->all(),
        ], 200);
    }
}
