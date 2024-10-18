<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analysis\UpdateAnalysisRequest;
use App\Http\Resources\AnalysisResource;
use App\Models\Analysis;
use App\Models\Employee;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AnalysisController extends Controller
{
    /**
     *   Función que muestra los análisis.
     * 
     *   @return JsonResponse Respuesta JSON con un mensaje de éxito
     *   y una colección con todos los análisis de la aplicación.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => AnalysisResource::collection(Analysis::all())
        ], 200);
    }

    /**
    *   Función para mostrar un análisis en concreto.
    *
    *   @param int ID del análisis a mostrar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos del análisis.
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
    *   Función para actualizar un análisis.
    *
    *   @param UpdateUserRequest Solicitud validada con los datos del nuevo análisis.
    *   @param int ID del análisis a actualizar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos del nuevo análisis.
    */
    public function update(UpdateAnalysisRequest $request, $id): JsonResponse
    {
        $analysis = Analysis::findOrFail($id);

        $analysis->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 200);
    }

    /**
    *   Función para eliminar un análisis.
    *
    *   @param int ID del análisis a eliminar.
    *   @return JsonResponse Respuesta JSON con un mensaje de éxito.
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

    public function indexForEmployee($employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $analyses = $employee->analyses;
        
        return response()->json([
            'status' => 'success',
            'data' => AnalysisResource::collection($analyses)
        ], 200);
    }

    public function updateForEmployee(UpdateAnalysisRequest $request, $employeeId, $analysisId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $analysis = $employee->analyses()->findOrFail($analysisId);

        $analysis->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 200);
    }

    public function showForEmployee($employeeId, $analysisId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $analysis = $employee->analyses()->findOrFail($analysisId);

        return response()->json([
            'status' => 'success',
            'data' => new AnalysisResource($analysis)
        ], 200);
    }

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
