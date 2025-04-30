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
     * @OA\Get(
     *     path="/api/analyses",
     *     summary="Listar todos los análisis de laboratorio",
     *     description="Devuelve una lista de todos los análisis de laboratorio registrados en el sistema. Requiere autenticación.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de análisis obtenida con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/AnalysisResource")
     *             )
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/analyses/{analysisId}",
     *     summary="Obtener un análisis específico por ID",
     *     description="Devuelve la información detallada de un análisis específico.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="analysisId",
     *         in="path",
     *         required=true,
     *         description="ID del análisis a mostrar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Análisis encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/AnalysisResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Análisis no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontró el análisis.")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/analyses/{analysisId}",
     *     summary="Actualizar un análisis de laboratorio",
     *     description="Actualiza un análisis existente, incluyendo su entrada asociada y crea un nuevo registro en el inventario de aceite. También se notifica al miembro por correo electrónico.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="analysisId",
     *         in="path",
     *         required=true,
     *         description="ID del análisis a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"analysis_date", "humidity", "acidity", "yield", "oil_id", "employee_id", "oil_quantity"},
     *             @OA\Property(property="analysis_date", type="string", format="date", example="2024-10-01"),
     *             @OA\Property(property="humidity", type="number", format="float", example=20.5),
     *             @OA\Property(property="acidity", type="number", format="float", example=0.3),
     *             @OA\Property(property="yield", type="number", format="float", example=18.5),
     *             @OA\Property(property="oil_id", type="integer", example=1),
     *             @OA\Property(property="employee_id", type="integer", example=3),
     *             @OA\Property(property="oil_quantity", type="number", format="float", example=125.6)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Análisis actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/AnalysisResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error al actualizar el análisis",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error al actualizar el análisis y la entrada")
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/analyses/{analysisId}",
     *     summary="Eliminar un análisis",
     *     description="Elimina un análisis de laboratorio del sistema.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="analysisId",
     *         in="path",
     *         required=true,
     *         description="ID del análisis a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Análisis eliminado correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Análisis eliminado satisfactoriamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Análisis no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se encontró el análisis.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/employees/{employeeId}/analyses",
     *     summary="Listar todos los análisis realizados por un empleado",
     *     description="Obtiene todos los análisis de aceituna que ha realizado un empleado específico.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de análisis obtenida correctamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AnalysisResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado no encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Empleado no encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/employees/{employeeId}/analyses/{analysisId}",
     *     summary="Obtener un análisis específico realizado por un empleado",
     *     description="Devuelve un análisis en particular asociado a un empleado específico.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         required=true,
     *         description="ID del empleado",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Parameter(
     *         name="analysisId",
     *         in="path",
     *         required=true,
     *         description="ID del análisis",
     *         @OA\Schema(type="integer", example=7)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Análisis obtenido exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/AnalysisResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Empleado o análisis no encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El análisis no pertenece al empleado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/members/{memberId}/analyses",
     *     summary="Listar todos los análisis de un socio",
     *     description="Obtiene todos los análisis de aceituna asociados a las entradas de un socio.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del socio",
     *         @OA\Schema(type="integer", example=2)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de análisis obtenida correctamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AnalysisResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Socio no encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Socio no encontrado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/members/{memberId}/entries/{entryId}/analyses",
     *     summary="Obtener el análisis asociado a una entrada de un agricultor",
     *     description="Devuelve el análisis vinculado a una entrada de aceituna específica para un socio determinado. Verifica que el usuario tiene el rol adecuado y que la entrada pertenece al socio.",
     *     tags={"Análisis"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del socio al que pertenece la entrada",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="entryId",
     *         in="path",
     *         required=true,
     *         description="ID de la entrada asociada al análisis",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Análisis obtenido exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/AnalysisResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. El usuario no tiene permiso para ver este análisis.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No tienes permiso para ver este análisis.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Socio, entrada o análisis no encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="El análisis no pertenece al socio.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     )
     * )
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
