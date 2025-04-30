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
use App\Models\OilSettlement;
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
     * 
     * @OA\Get(
     *     path="/api/settlements",
     *     summary="Obtener todas las liquidaciones de aceite",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de liquidaciones de aceite",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SettlementResource"))
     *         )
     *     ),
     *   @OA\Response(
     *        response=401,
     *        description="No autorizado",
     *        @OA\JsonContent(
     *            @OA\Property(property="status", type="string", example="error"),
     *            @OA\Property(property="message", type="string", example="No autorizado")
     *        )
     *     ),
     * )
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
     * 
     * @OA\Post(
     *     path="/api/settlements",
     *     summary="Crear una nueva liquidación de aceite",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="settlement_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="oil_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=100.0),
     *             @OA\Property(property="price", type="number", format="float", example=10.0),
     *             @OA\Property(property="settlement_status", type="string", example="Pendiente"),
     *             @OA\Property(property="member_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Liquidación creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/SettlementResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string", example="El campo es obligatorio")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
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
     * 
     * @OA\Post(
     *    path="/api/settlementsAvailable",
     *    summary="Crear una nueva liquidación de aceite disponible",
     *    tags={"Liquidaciones"},
     *    security={{"bearerAuth": {}}},
     *    @OA\RequestBody(
     *        required=true,
     *        @OA\JsonContent(
     *            @OA\Property(property="settlement_date", type="string", format="date", example="2023-10-01"),
     *            @OA\Property(property="oil_id", type="integer", example=1),
     *            @OA\Property(property="amount", type="number", format="float", example=100.0),
     *            @OA\Property(property="price", type="number", format="float", example=10.0),
     *            @OA\Property(property="settlement_status", type="string", example="Pendiente"),
     *            @OA\Property(property="member_id", type="integer", example=1)
     *        )
     *    ),
     *    @OA\Response(
     *        response=201,
     *        description="Liquidación creada exitosamente",
     *        @OA\JsonContent(
     *            @OA\Property(property="status", type="string", example="success"),
     *            @OA\Property(property="data", ref="#/components/schemas/SettlementResource")
     *        )
     *    ),
     *    @OA\Response(
     *        response=400,
     *        description="Error de insuficiencia de aceite disponible",
     *        @OA\JsonContent(
     *            @OA\Property(property="status", type="string", example="error"),
     *            @OA\Property(property="message", type="string", example="No hay suficiente aceite disponible para esta liquidación.")
     *        )
     *    ),
     *    @OA\Response(
     *        response=500,
     *        description="Error interno del servidor",
     *        @OA\JsonContent(
     *            @OA\Property(property="status", type="string", example="error"),
     *            @OA\Property(property="message", type="string", example="Error al crear la liquidación.")
     *        )
     *    )
     * )
     */
    public function storeAvailable(StoreSettlementRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $totalInventory = OilInventory::where('member_id', $validated['member_id'])
                ->where('oil_id', $validated['oil_id'])
                ->sum('quantity');

            $totalSettled = OilSettlement::where('member_id', $validated['member_id'])
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
     * 
     * @OA\Get(
     *     path="/api/settlements/{id}",
     *     summary="Obtener una liquidación de aceite por ID",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la liquidación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liquidación encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/SettlementResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Liquidación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Liquidación no encontrada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
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
     * 
     * @OA\Put(
     *     path="/api/settlements/{id}",
     *     summary="Actualizar una liquidación de aceite por ID",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la liquidación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="settlement_date", type="string", format="date", example="2023-10-01"),
     *             @OA\Property(property="oil_id", type="integer", example=1),
     *             @OA\Property(property="amount", type="number", format="float", example=100.0),
     *             @OA\Property(property="price", type="number", format="float", example=10.0),
     *             @OA\Property(property="settlement_status", type="string", example="Pendiente"),
     *             @OA\Property(property="member_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liquidación actualizada exitosamente",  
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/SettlementResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Liquidación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Liquidación no encontrada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\AdditionalProperties(
     *                     type="array",
     *                     @OA\Items(type="string", example="El campo es obligatorio")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
     */
    public function update(UpdateSettlementRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {
            $settlement = Settlement::findOrFail($id);
            $settlement->update($request->validated());

            if ($settlement->settlement_status === 'Aceptada') {
                OilSettlement::create([
                    'member_id' => $settlement->member_id,
                    'oil_id' => $settlement->oil_id,
                    'amount' => $settlement->amount,
                    'settlement_date' => $settlement->settlement_date,
                ]);
            }

            DB::commit();

            $memberEmail = $settlement->member->user->email;
            $settlementResource = (new SettlementResource($settlement))->toArray($request);

            $pdf = Pdf::loadView('pdf.new_settlement', ['settlement' => $settlementResource]);

            Mail::to($memberEmail)->send(new NewSettlementUpdated($settlementResource, $pdf->output()));

            return response()->json([
                'status' => 'success',
                'data' => new SettlementResource($settlement)
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar la liquidación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina una liquidación específica por su ID.
     * 
     * Este método recibe un ID de liquidación, busca la liquidación en la base de datos y elimina la liquidación.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID de la liquidación.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     * 
     * @OA\Delete(
     *     path="/api/settlements/{id}",
     *     summary="Eliminar una liquidación de aceite por ID",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la liquidación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liquidación eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Liquidación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Liquidación no encontrada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
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
     * 
     * @OA\Delete(
     *     path="/api/members/{memberId}/settlements/{settlementId}",
     *     summary="Eliminar una liquidación de aceite por ID del miembro",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del miembro",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="settlementId",
     *         in="path",
     *         required=true,
     *         description="ID de la liquidación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liquidación eliminada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Liquidación eliminada correctamente.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No tienes permiso para eliminar esta liquidación.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Liquidación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Liquidación no encontrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/api/members/{memberId}/settlements",
     *     summary="Obtener liquidaciones de aceite de un socio por ID",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del socio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liquidaciones encontradas",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SettlementResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Socio no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Socio no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
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
     * 
     * @OA\Get(
     *     path="/api/members/{memberId}/settlements/{settlementId}",
     *     summary="Obtener una liquidación de aceite de un socio por ID",
     *     tags={"Liquidaciones"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del socio",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="settlementId",
     *         in="path",
     *         required=true,
     *         description="ID de la liquidación",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liquidación encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/SettlementResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Liquidación no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Liquidación no encontrada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No autorizado")
     *         )
     *     )
     * )
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
