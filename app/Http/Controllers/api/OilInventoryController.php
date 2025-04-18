<?php

namespace App\Http\Controllers;

use App\Http\Requests\OilInventory\StoreOilInventoryRequest;
use App\Http\Resources\OilInventoryResource;
use App\Models\OilInventory;

class OilInventoryController extends Controller
{
    public function store(StoreOilInventoryRequest $request)
    {
        $oilInventory = OilInventory::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilInventoryResource($oilInventory)
        ], 201);
    }
}
