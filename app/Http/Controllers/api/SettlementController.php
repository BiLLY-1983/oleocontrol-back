<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settlement\StoreSettlementRequest;
use App\Http\Requests\Settlement\UpdateSettlementRequest;
use App\Http\Resources\SettlementResource;
use App\Mail\NewSettlementUpdated;
use App\Models\Employee;
use App\Models\Member;
use App\Models\OilInventory;
use App\Models\Settlement;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SettlementController extends Controller
{
    /**
     * Muestra todas las liquidaciones de aceite.
     * 
     * Este método obtiene todas las liquidaciones de aceite de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de las liquidaciones.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de las liquidaciones.
     */
    public function index(): JsonResponse
    {
        $settlements = Settlement::all();

        return response()->json([
            'status' => 'success',
            'data' => SettlementResource::collection($settlements)
        ], 200);
    }

    /**
     * Crea una nueva liquidación de aceite.
     * 
     * Este método recibe una solicitud de creación de liquidación, valida los datos y crea una nueva liquidación en la base de datos.
     * La respuesta incluye un estado de éxito y los datos de la liquidación creada en formato JSON.
     *
     * @param StoreSettlementRequest $request La solicitud de creación de liquidación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la liquidación creada.
     */
    public function store(StoreSettlementRequest $request): JsonResponse
    {
        $settlement = Settlement::create($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 201);
    }

    /**
     * Crea una nueva liquidación de aceite disponible.
     * 
     * Este método recibe una solicitud de creación de liquidación disponible, valida los datos, realiza los cálculos de aceite disponible y crea una nueva liquidación sin modificar el inventario.
     * Utiliza una transacción para asegurar que la liquidación se cree correctamente o se revierta en caso de error.
     *
     * @param StoreSettlementRequest $request La solicitud de creación de liquidación disponible.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la liquidación creada, o un error en caso de insuficiencia de aceite disponible.
     */
    public function storeAvailable(StoreSettlementRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $totalInventory = OilInventory::where('member_id', $validated['member_id'])
                ->where('oil_id', $validated['oil_id'])
                ->sum('quantity');

            $totalSettled = Settlement::where('member_id', $validated['member_id'])
                ->where('oil_id', $validated['oil_id'])
                ->sum('amount');

            $availableOil = $totalInventory - $totalSettled;

            if ($availableOil < $validated['amount']) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'No hay suficiente aceite disponible para esta liquidación. Disponible: ' . $availableOil . ' litros.'
                ], 400);
            }

            $settlement = Settlement::create([
                'settlement_date' => $validated['settlement_date'],
                'oil_id' => $validated['oil_id'],
                'amount' => $validated['amount'],
                'price' => $validated['price'],
                'settlement_status' => $validated['settlement_status'],
                'member_id' => $validated['member_id'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => new SettlementResource($settlement)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la liquidación: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Muestra una liquidación específica por su ID.
     * 
     * Este método recibe un ID de liquidación, busca la liquidación en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de la liquidación.
     *
     * @param int $id El ID de la liquidación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la liquidación.
     */
    public function show($id): JsonResponse
    {
        $settlement = Settlement::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }

    /**
     * Actualiza una liquidación específica por su ID.
     * 
     * Este método recibe una solicitud de actualización de liquidación, valida los datos y actualiza la liquidación en la base de datos.
     * La respuesta incluye un estado de éxito y los datos de la liquidación actualizada en formato JSON.
     *
     * @param UpdateSettlementRequest $request La solicitud de actualización de liquidación.
     * @param int $id El ID de la liquidación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la liquidación actualizada.
     */
    public function update(UpdateSettlementRequest $request, $id): JsonResponse
    {
        $settlement = Settlement::findOrFail($id);
        $settlement->update($request->validated());


        $memberEmail = $settlement->member->user->email;
        $settlementResource = (new SettlementResource($settlement))->toArray($request);

        // Generar el PDF
        $pdf = Pdf::loadView('pdf.new_settlement', ['settlement' => $settlementResource]);

        // Enviar el correo con el PDF adjunto
        Mail::to($memberEmail)->send(new NewSettlementUpdated($settlementResource, $pdf->output()));

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }

    /**
     * Elimina una liquidación específica por su ID.
     * 
     * Este método recibe un ID de liquidación, busca la liquidación en la base de datos y elimina la liquidación.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID de la liquidación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     */
    public function destroy($id): JsonResponse
    {
        $settlement = Settlement::findOrFail($id);
        $settlement->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     * Elimina una liquidación asociada al miembro que realizó la liquidación.
     * 
     * Este método permite a un socio eliminar una liquidación que haya creado, siempre y cuando el usuario esté autenticado como 'Socio' y la liquidación pertenezca al miembro correspondiente.
     * La respuesta incluye un estado de éxito o un mensaje de error si no se tienen permisos o si la liquidación no se encuentra.
     *
     * @param int $memberId El ID del miembro.
     * @param int $settlementId El ID de la liquidación a eliminar.
     * @return JsonResponse Respuesta JSON con el estado de éxito o un mensaje de error.
     */
    public function destroyOwn($memberId, $settlementId): JsonResponse
    {
        $user = Auth::user();

        if (!$user->roles->contains('name', 'Socio')) {
            return response()->json(['message' => 'No eres un Socio.'], 403);
        }

        $member = $user->member;

        if (!$member || $member->id != $memberId) {
            return response()->json(['message' => 'No tienes permiso para eliminar esta liquidación.'], 403);
        }

        $settlement = $member->settlements()->find($settlementId);

        if (!$settlement || $settlement->member_id != $member->id) {
            return response()->json(['message' => 'Esta liquidación no pertenece al socio.'], 403);
        }

        $settlementToDelete = Settlement::where('id', $settlementId)
            ->where('member_id', $memberId)
            ->first();

        if (!$settlementToDelete) {
            return response()->json([
                'status' => 'error',
                'message' => 'Liquidación no encontrada.',
            ], 404);
        }

        $settlementToDelete->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Liquidación eliminada correctamente.',
        ], 200);
    }


    /**
     * Muestra las liquidaciones de aceite de un empleado específico por su ID.
     * 
     * Este método recibe un ID de empleado, busca el empleado en la base de datos y devuelve una respuesta JSON con un estado de éxito y las liquidaciones de aceite del empleado.
     *
     * @param int $employeeId El ID del empleado.
     * @return JsonResponse Respuesta JSON con el estado de éxito y las liquidaciones de aceite del empleado.
     */
    public function indexForEmployee($employeeId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $settlements = $employee->settlements;

        return response()->json([
            'status' => 'success',
            'data' => SettlementResource::collection($settlements)
        ], 200);
    }

    /**
     * Muestra una liquidación específica de un empleado por su ID.
     * 
     * Este método recibe un ID de empleado y un ID de liquidación, busca la liquidación asociada al empleado en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de la liquidación.
     *
     * @param int $employeeId El ID del empleado.
     * @param int $settlementId El ID de la liquidación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la liquidación.
     */
    public function showForEmployee($employeeId, $settlementId): JsonResponse
    {
        $employee = Employee::findOrFail($employeeId);
        $settlement = $employee->settlements()->findOrFail($settlementId);

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }

    /**
     * Muestra las liquidaciones de aceite de un socio específico por su ID.
     * 
     * Este método recibe un ID de socio, busca el socio en la base de datos y devuelve una respuesta JSON con un estado de éxito y las liquidaciones de aceite del socio.
     *
     * @param int $memberId El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y las liquidaciones de aceite del socio.
     */
    public function indexForMember($memberId): JsonResponse
    {
        $member = Member::findOrFail($memberId);
        $settlements = $member->settlements;

        return response()->json([
            'status' => 'success',
            'data' => SettlementResource::collection($settlements)
        ], 200);
    }

    /**
     * Muestra una liquidación específica de un socio por su ID.
     * 
     * Este método recibe un ID de socio y un ID de liquidación, busca la liquidación asociada al socio en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de la liquidación.
     *
     * @param int $memberId El ID del socio.
     * @param int $settlementId El ID de la liquidación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la liquidación.
     */
    public function showForMember($memberId, $settlementId): JsonResponse
    {
        $member = Member::findOrFail($memberId);
        $settlement = $member->settlements()->findOrFail($settlementId);

        return response()->json([
            'status' => 'success',
            'data' => new SettlementResource($settlement)
        ], 200);
    }
}
