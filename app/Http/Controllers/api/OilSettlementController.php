<?php

namespace App\Http\Controllers;

use App\Http\Requests\OilSettlement\StoreOilSettlementRequest;
use App\Http\Resources\OilSettlementResource;
use App\Models\OilSettlement;

class OilSettlementController extends Controller
{
    /**
     * Crea un nuevo acuerdo de liquidación de aceite.
     * 
     * Este método recibe una solicitud de creación de acuerdo de liquidación de aceite, valida los datos y crea un nuevo registro en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del acuerdo de liquidación de aceite creado en formato JSON.
     *
     * @param StoreOilSettlementRequest $request La solicitud de creación de acuerdo de liquidación de aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del acuerdo de liquidación de aceite creado.
     */
    public function store(StoreOilSettlementRequest $request)
    {
        $oilSettlement = OilSettlement::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilSettlementResource($oilSettlement)
        ], 201);
    }
}
