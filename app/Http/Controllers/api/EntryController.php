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
     *   Función que muestra las entradas de aceituna.
     * 
     *   @return JsonResponse Respuesta JSON con un mensaje de éxito
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EntryResource::collection(Entry::all())
        ], 200);
    }

    /**
    *   Función para agregar una nueva entrada de aceituna.
    *   Cuando se crea una entrada, se crea automaticamente una análisis.
    *
    *   @param StoreUserRequest Solicitud validada con los datos de la nueva entrada.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos de la entrada creada.
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
    *   Función para actualizar una entrada.
    *
    *   @param UpdateUserRequest Solicitud validada con los datos de la nueva entrada.
    *   @param int ID de la entrada a actualizar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos de la entrada.
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
    *   Función para eliminar una entrada.
    *
    *   @param int ID de la entrada a eliminar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito.
    */
    public function destroy($id): JsonResponse
    {
        $entry = Entry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    public function indexForMember($memberId): JsonResponse
    {     
        $member = Member::findOrFail($memberId);
        $entries = $member->entries;

        return response()->json([
            'status' => 'success',
            'data' => EntryResource::collection($entries)
        ], 200);
    }

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
