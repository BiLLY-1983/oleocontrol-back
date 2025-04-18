<?php

namespace App\Http\Controllers;

use App\Http\Requests\OilSettlement\StoreOilSettlementRequest;
use App\Http\Resources\OilSettlementResource;
use App\Models\OilSettlement;

class OilSettlementController extends Controller
{
    public function store(StoreOilSettlementRequest $request)
    {
        $oilSettlement = OilSettlement::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilSettlementResource($oilSettlement)
        ], 201);
    }
}
