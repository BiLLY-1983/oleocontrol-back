<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analysis\UpdateAnalysisRequest;
use App\Http\Resources\AnalysisResource;
use App\Mail\NewAnalysisUpdated;
use App\Models\Analysis;
use App\Models\Employee;
use App\Models\Entry;
use App\Models\Member;
use App\Models\OilInventory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalysisController extends Controller
{
    /**
     * Muestra los análisis.
     * 
     * Este método recupera todos los análisis de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los análisis.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los análisis.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => AnalysisResource::collection(Analysis::all())
        ], 200);
    }

    /**
     * Muestra un análisis en concreto.
     * 
     * Este método recibe un ID de análisis, busca el análisis en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del análisis.
     *
     * @param int $id El ID del análisis a mostrar.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del análisis.
     */
    public function show($id): JsonResponse
    {
        $analysis = Analysis::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 200);
    }

    /**
     * Actualiza un análisis.
     * 
     * Este método recibe una solicitud de actualización de análisis, valida los datos y actualiza el análisis en la base de datos.
     * Una vez actualizado el análisis, se actualiza la entrada asociada en la tabla Entries y se genera una entrada en la
     * tabla inventarios de aceite con la cantidad y el tipo de aceite perteneciente al socio al que pertenece ese análisis.
     * La respuesta incluye un estado de éxito y los datos del análisis actualizado en formato JSON.
     *
     * @param UpdateAnalysisRequest $request La solicitud de actualización de análisis.
     * @param int $id El ID del análisis a actualizar.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del análisis actualizado.
     */
    public function update(UpdateAnalysisRequest $request, $id): JsonResponse
    {
        // Iniciar la transacción
        DB::beginTransaction();

        try {
            $analysis = Analysis::findOrFail($id);

            $data = $request->validated();

            $analysis->update([
                'analysis_date' => $data['analysis_date'],
                'humidity' => $data['humidity'],
                'acidity' => $data['acidity'],
                'yield' => $data['yield'],
                'oil_id' => $data['oil_id'],
                'employee_id' => $data['employee_id']
            ]);

            $entry = Entry::findOrFail($analysis->entry_id);

            // Actualizar la entrada asociada
            $entry->update([
                'oil_quantity' => $data['oil_quantity'],
                'analysis_status' => 'Completo'
            ]);

            // Crear entrada en el inventario
            OilInventory::create([
                'member_id' => $entry->member_id,
                'oil_id' => $data['oil_id'],
                'quantity' => $data['oil_quantity'],
            ]);

            // Si todo ha ido bien, confirmamos la transacción
            DB::commit();

            $memberEmail = $entry->member->user->email;
            $analysisResource = (new AnalysisResource($analysis))->toArray($request);

            // Generar el PDF
            $pdf = Pdf::loadView('pdf.new_analysis', ['analysis' => $analysisResource]);

            // Enviar el email conel nuevo análisis
            Mail::to($memberEmail)->send(new NewAnalysisUpdated($analysisResource, $pdf->output()));

            return response()->json([
                'status' => 'success',
                'data' => new AnalysisResource($analysis)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el análisis y la entrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un análisis.
     * 
     * Este método recibe un ID de análisis, busca el análisis en la base de datos y elimina el análisis.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del análisis a eliminar.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     */
    public function destroy($id): JsonResponse
    {
        $analysis = Analysis::findOrFail($id);
        $analysis->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Análisis eliminado satisfactoriamente.'
        ], 200);
    }

    /**
     * Muestra los análisis de un empleado.
     * 
     * Este método recibe un ID de empleado, busca el empleado en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los análisis del empleado.
     *
     * @param int $employeeId El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los análisis del empleado.
     */
    public function indexForEmployee($employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $analyses = $employee->analyses;

        return response()->json([
            'status' => 'success',
            'data' => AnalysisResource::collection($analyses)
        ], 200);
    }

    /**
     * Muestra un análisis de un empleado.
     * 
     * Este método recibe un ID de empleado y un ID de análisis, busca el análisis en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del análisis.
     *
     * @param int $employeeId El ID del empleado.
     * @param int $analysisId El ID del análisis.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del análisis.
     */
    public function showForEmployee($employeeId, $analysisId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $analysis = $employee->analyses()->findOrFail($analysisId);

        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 200);
    }

    /**
     * Muestra los análisis de un socio.
     * 
     * Este método recibe un ID de socio, busca el socio en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los análisis del socio.
     *
     * @param int $memberId El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los análisis del socio.
     */
    public function indexForMember($memberId): JsonResponse
    {
        $member = Member::findOrFail($memberId);

        $analyses = $member->entries->map(function ($entry) {
            return $entry->analysis;
        })->filter();


        return response()->json([
            'status' => 'success',
            'data' => AnalysisResource::collection($analyses)
        ], 200);
    }

    /**
     * Muestra un análisis de un socio.
     * 
     * Este método recibe un ID de socio y un ID de entrada, busca la entrada en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del análisis.
     *
     * @param int $memberId El ID del socio.
     * @param int $entryId El ID de la entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del análisis.
     */
    public function showForMember($memberId, $entryId): JsonResponse
    {
        $member = Member::findOrFail($memberId);
        $entry = $member->entries()->findOrFail($entryId);
        $analysis = $entry->analysis;

        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 200);
    }
}
