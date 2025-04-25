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
