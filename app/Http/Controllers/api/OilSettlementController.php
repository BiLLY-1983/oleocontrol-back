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
