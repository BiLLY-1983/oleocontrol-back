<?php

namespace App\Http\Controllers;

use App\Http\Requests\OilInventory\StoreOilInventoryRequest;
use App\Http\Resources\OilInventoryResource;
use App\Models\OilInventory;

class OilInventoryController extends Controller
{
    /**
     * Crea un nuevo inventario de aceite.
     * 
     * Este método recibe una solicitud de creación de inventario de aceite, valida los datos y crea un nuevo registro en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del inventario de aceite creado en formato JSON.
     *
     * @param StoreOilInventoryRequest $request La solicitud de creación de inventario de aceite.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del inventario de aceite creado.
     */
    public function store(StoreOilInventoryRequest $request)
    {
        $oilInventory = OilInventory::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new OilInventoryResource($oilInventory)
        ], 201);
    }
}
