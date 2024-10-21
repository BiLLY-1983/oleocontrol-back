<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analysis\StoreAnalysisRequest;
use App\Http\Requests\Entry\StoreEntryRequest;
use App\Http\Requests\Entry\UpdateEntryRequest;
use App\Http\Resources\AnalysisResource;
use App\Http\Resources\EntryResource;
use App\Models\Analysis;
use App\Models\Entry;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    /**
     * Muestra todas las entradas de aceituna.
     * 
     * Este método obtiene todas las entradas de aceituna de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de las entradas.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de las entradas.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EntryResource::collection(Entry::all())
        ], 200);
    }

    /**
     * Crea una nueva entrada de aceituna.
     * 
     * Este método recibe una solicitud de creación de entrada, valida los datos y crea una nueva entrada en la base de datos.
     * La respuesta incluye un estado de éxito y los datos de la entrada creada en formato JSON.
     *
     * @param StoreEntryRequest $request La solicitud de creación de entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la entrada creada.
     */
    public function store(StoreEntryRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $entry = Entry::create($request->validated());
            
            // Crear automáticamente un análisis asociado
            $analysisData = [
                'entry_id' => $entry->id,
                'analysis_date' => now(),
            ];

            $analysisRequest = new StoreAnalysisRequest($analysisData);
            $validatedAnalysisData = $analysisRequest->validated();
            
            $analysis = $entry->analysis()->create($validatedAnalysisData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'entry' => new EntryResource($entry),
                    'analysis' => new AnalysisResource($analysis)
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la entrada y el análisis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra una entrada específica por su ID.
     * 
     * Este método recibe un ID de entrada, busca la entrada en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de la entrada.
     *
     * @param int $id El ID de la entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la entrada.
     */
    public function show($id): JsonResponse
    {
        $entry = Entry::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 200);
    }

    /**
     * Actualiza una entrada específica por su ID.
     * 
     * Este método recibe una solicitud de actualización de entrada, valida los datos y actualiza la entrada en la base de datos.
     * La respuesta incluye un estado de éxito y los datos de la entrada actualizada en formato JSON.
     *
     * @param UpdateEntryRequest $request La solicitud de actualización de entrada.
     * @param int $id El ID de la entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la entrada actualizada.
     */
    public function update(UpdateEntryRequest $request, $id): JsonResponse
    {
        $entry = Entry::findOrFail($id);
        $entry->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 200);
    }

    /**
     * Elimina una entrada específica por su ID.
     * 
     * Este método recibe un ID de entrada, busca la entrada en la base de datos y elimina la entrada.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID de la entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     */
    public function destroy($id): JsonResponse
    {
        $entry = Entry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * Muestra todas las entradas de un miembro específico por su ID.
     * 
     * Este método recibe un ID de miembro, busca los entradas asociadas al miembro en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de las entradas.
     *
     * @param int $memberId El ID del miembro.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de las entradas.
     */
    public function indexForMember($memberId): JsonResponse
    {     
        $member = Member::findOrFail($memberId);
        $entries = $member->entries;

        return response()->json([
            'status' => 'success',
            'data' => EntryResource::collection($entries)
        ], 200);
    }

    /**
     * Muestra una entrada específica de un miembro por su ID.
     * 
     * Este método recibe un ID de miembro y un ID de entrada, busca la entrada asociada al miembro en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de la entrada.
     *
     * @param int $memberId El ID del miembro.
     * @param int $entryId El ID de la entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la entrada.
     */
    public function showForMember($memberId, $entryId): JsonResponse
    {
        $member = Member::findOrFail($memberId);
        $entry = $member->entries()->findOrFail($entryId);

        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 200);
    }
}
