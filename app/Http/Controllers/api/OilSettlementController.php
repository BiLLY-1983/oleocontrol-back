<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OilSettlement\StoreOilSettlementRequest;
use App\Http\Resources\OilSettlementResource;
use App\Models\Member;
use App\Models\OilSettlement;
use Illuminate\Http\JsonResponse;

class OilSettlementController extends Controller
{
    /**
     * Muestra un listado de todos los acuerdos de liquidación de aceite.
     * 
     * @return JsonResponse Respuesta JSON con la lista de acuerdos de liquidación.
     * 
     * @OA\Get(
     *     path="/api/oil-settlements",
     *     summary="Listar acuerdos de liquidación de aceite",
     *     description="Obtiene una lista de todos los acuerdos de liquidación de aceite.",
     *     tags={"Liquidaciones de aceite aceptadas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de acuerdos de liquidación de aceite",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/OilSettlementResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
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
     *             @OA\Property(property="message", type="string", example="Error interno del servidor")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $settlements = OilSettlement::all();

        return response()->json([
            'status' => 'success',
            'data' => OilSettlementResource::collection($settlements),
        ]);
    }

    /**
     * Muestra los detalles de un acuerdo de liquidación de aceite específico.
     * 
     * @param OilSettlement $oilSettlement El acuerdo de liquidación a mostrar.
     * @return JsonResponse Respuesta JSON con los datos del acuerdo de liquidación.
     * 
     * @OA\Get(
     *     path="/api/oil-settlements/{id}",
     *     summary="Mostrar acuerdo de liquidación de aceite",
     *     description="Obtiene los detalles de un acuerdo de liquidación de aceite específico.",
     *     tags={"Liquidaciones de aceite aceptadas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del acuerdo de liquidación de aceite",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del acuerdo de liquidación de aceite",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/OilSettlementResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Recurso no encontrado.")
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
    public function show(OilSettlement $oilSettlement): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new OilSettlementResource($oilSettlement),
        ]);
    }

    /**
     * Crea un nuevo acuerdo de liquidación de aceite.
     * 
     * Este método recibe una solicitud de creación de acuerdo de liquidación de aceite, valida los datos y crea un nuevo registro en la base de datos.
     * 
     * @param StoreOilSettlementRequest $request La solicitud de creación de acuerdo de liquidación de aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del acuerdo de liquidación creado.
     * 
     * @OA\Post(
     *     path="/api/oil-settlements",
     *     summary="Crear acuerdo de liquidación de aceite",
     *     description="Crea un nuevo acuerdo de liquidación de aceite.",
     *     tags={"Liquidaciones de aceite aceptadas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="member_id", type="integer", example=1),
     *             @OA\Property(property="oil_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=100.50),
     *             @OA\Property(property="settlement_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="notes", type="string", example="Acuerdo de liquidación mensual")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Acuerdo de liquidación creado con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/OilSettlementResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error de validación.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
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
     *             @OA\Property(property="message", type="string", example="Error interno del servidor")
     *         )
     *     )
     * )
     */
    public function store(StoreOilSettlementRequest $request): JsonResponse
    {
        $oilSettlement = OilSettlement::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilSettlementResource($oilSettlement),
        ], 201);
    }


    /**
     * Muestra todos los acuerdos de liquidación de aceite asociados a un socio específico y calcula el total por tipo de aceite.
     * 
     * Este método busca el socio por su ID, devuelve todos sus acuerdos de liquidación de aceite y calcula el total por tipo de aceite.
     * Se utiliza el recurso OilSettlementResource para formatear la respuesta.
     *
     * @param int $memberId ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito, los acuerdos de liquidación de aceite y el total por tipo de aceite.
     * 
     * @OA\Get(
     *     path="/api/members/{memberId}/oil-settlements",
     *     summary="Listar acuerdos de liquidación de aceite por socio",
     *     description="Obtiene todos los acuerdos de liquidación de aceite asociados a un socio específico y calcula el total por tipo de aceite.",
     *     tags={"Liquidaciones de aceite aceptadas"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del socio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de acuerdos de liquidación de aceite por socio",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="oil_id", type="integer", example=1),
     *                 @OA\Property(property="oil_name", type="string", example="Aceite de Oliva"),
     *                 @OA\Property(property="total_amount", type="number", format="float", example=100.50)
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Recurso no encontrado.")
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
        // Cargar el miembro y la relación 'oilSettlements'
        $member = Member::findOrFail($memberId);
        $oil_settlements = $member->oilSettlements;

        // Verificar si la colección está vacía
        if ($oil_settlements->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'data' => [],  // Devuelve un array vacío
            ], 200);
        }

        // Agrupar las liquidaciones por tipo de aceite
        $groupedByOil = $oil_settlements->groupBy('oil_id');

        // Sumar las cantidades por tipo de aceite
        $totalByOil = $groupedByOil->map(function ($settlements) {
            // Asegúrate de que existe un aceite asociado
            $firstSettlement = $settlements->first();
            $oil = $firstSettlement->oil;

            return [
                'oil_id' => $firstSettlement->oil_id,
                'oil_name' => $oil ? $oil->name : 'Desconocido',  // Controlar si no hay aceite
                'total_amount' => $settlements->sum('amount'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $totalByOil->values()->all(),
        ], 200);
    }
}
