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
