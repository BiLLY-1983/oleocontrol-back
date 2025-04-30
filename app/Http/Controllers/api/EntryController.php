<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Analysis\StoreAnalysisRequest;
use App\Http\Requests\Entry\StoreEntryRequest;
use App\Http\Requests\Entry\UpdateEntryRequest;
use App\Http\Resources\AnalysisResource;
use App\Http\Resources\EntryResource;
use App\Mail\NewEntryCreated;
use App\Models\Entry;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class EntryController extends Controller
{
    /**
     * Muestra todas las entradas de aceituna.
     * 
     * Este método obtiene todas las entradas de aceituna de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de las entradas.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de las entradas.
     * @OA\Get(
     *     path="/entries",
     *     summary="Obtener todas las entradas de aceituna",
     *     description="Este método obtiene todas las entradas de aceituna de la base de datos.",
     *     tags={"Entradas de Aceituna"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de entradas obtenida con éxito",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/EntryResource"))
     *         )
     *     )
     * )
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
     * Si se ha creado la entrada de aceituna satisfactoriamente, se genera un análisis asociado a esa entrada.
     * La respuesta incluye un estado de éxito y los datos de la entrada creada en formato JSON.
     *
     * @param StoreEntryRequest $request La solicitud de creación de entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la entrada creada.
     * @OA\Post(
     *     path="/api/entries",
     *     summary="Crear una nueva entrada de aceituna",
     *     description="Crea una nueva entrada de aceituna y genera un análisis asociado. Envía un correo con un PDF de la entrada creada al agricultor.",
     *     tags={"Entradas de Aceituna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"member_id", "weight", "variety", "harvest_date", "olive_type"},
     *             @OA\Property(property="member_id", type="integer", example=1),
     *             @OA\Property(property="weight", type="number", format="float", example=1500.5),
     *             @OA\Property(property="variety", type="string", example="Arbequina"),
     *             @OA\Property(property="harvest_date", type="string", format="date", example="2025-04-28"),
     *             @OA\Property(property="olive_type", type="string", example="Verde")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Entrada de aceituna creada exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/EntryResource"),
     *             @OA\Property(property="analysis", type="object", ref="#/components/schemas/AnalysisResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta, falta algún dato requerido o los datos son incorrectos.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="El campo 'weight' es obligatorio.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No se pudo crear la entrada y el análisis.")
     *         )
     *     )
     * )
     */
    public function store(StoreEntryRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $entry = Entry::create($request->validated());

            $analysis = $entry->analysis()->create([
                'member_id' => $request->member_id,
            ]);

            DB::commit();

            $memberEmail = $entry->member->user->email;
            $entryResource = (new EntryResource($entry))->toArray($request);

            // Generar el PDF
            $pdf = Pdf::loadView('pdf.new_entry', ['entry' => $entryResource]);

            // Enviar el email con el PDF adjunto
            Mail::to($memberEmail)->send(new NewEntryCreated($entryResource, $pdf->output()));

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
     * @OA\Get(
     *     path="/api/entries/{id}",
     *     summary="Obtener detalles de una entrada de aceituna",
     *     description="Recupera los detalles de una entrada de aceituna específica mediante su ID.",
     *     tags={"Entradas de Aceituna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la entrada de aceituna",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entrada de aceituna obtenida exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/EntryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entrada no encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="La entrada de aceituna con el ID 1 no existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No se pudo recuperar la entrada de aceituna.")
     *         )
     *     )
     * )
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
     * @OA\Put(
     *     path="/api/entries/{id}",
     *     summary="Actualizar una entrada de aceituna",
     *     description="Actualiza los detalles de una entrada de aceituna específica mediante su ID.",
     *     tags={"Entradas de Aceituna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la entrada de aceituna a actualizar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"date", "weight", "oil_content", "price"},
     *             @OA\Property(property="date", type="string", format="date", example="2025-04-29"),
     *             @OA\Property(property="weight", type="number", format="float", example=2500),
     *             @OA\Property(property="oil_content", type="number", format="float", example=18.5),
     *             @OA\Property(property="price", type="number", format="float", example=5.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entrada de aceituna actualizada exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object", ref="#/components/schemas/EntryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud incorrecta, falta algún dato requerido.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="El campo 'weight' es obligatorio.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entrada no encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="La entrada de aceituna con el ID 1 no existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No se pudo actualizar la entrada de aceituna.")
     *         )
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/entries/{id}",
     *     summary="Eliminar una entrada de aceituna",
     *     description="Elimina una entrada de aceituna específica mediante su ID.",
     *     tags={"Entradas de Aceituna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la entrada de aceituna a eliminar",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entrada de aceituna eliminada exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entrada no encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="La entrada de aceituna con el ID 1 no existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No se pudo eliminar la entrada de aceituna.")
     *         )
     *     )
     * )
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
     * @param int $memberId El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de las entradas.
     * @OA\Get(
     *     path="/api/members/{memberId}/entries",
     *     summary="Obtener todas las entradas de aceituna de un miembro",
     *     description="Obtiene todas las entradas de aceituna asociadas a un miembro específico utilizando su ID.",
     *     tags={"Entradas de Aceituna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del miembro para obtener sus entradas de aceituna",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entradas de aceituna obtenidas exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/EntryResource"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Miembro no encontrado.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="El miembro con el ID 1 no existe.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No se pudieron obtener las entradas de aceituna.")
     *         )
     *     )
     * )
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
     * @param int $memberId El ID del socio.
     * @param int $entryId El ID de la entrada.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de la entrada.
     * @OA\Get(
     *     path="/api/members/{memberId}/entries/{entryId}",
     *     summary="Obtener una entrada de aceituna de un miembro específico",
     *     description="Obtiene una entrada de aceituna específica asociada a un miembro, verificando que el usuario tiene el rol adecuado y que pertenece al miembro correcto.",
     *     tags={"Entradas de Aceituna"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="memberId",
     *         in="path",
     *         required=true,
     *         description="ID del miembro al que pertenece la entrada de aceituna",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="entryId",
     *         in="path",
     *         required=true,
     *         description="ID de la entrada de aceituna que se desea obtener",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Entrada de aceituna obtenida exitosamente.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", ref="#/components/schemas/EntryResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Acceso denegado. El usuario no tiene permiso para ver esta entrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No tienes permiso para ver esta entrada.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Miembro o entrada no encontrada.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="La entrada no pertenece al socio.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado. Se requiere autenticación.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Acceso denegado.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="No se pudo obtener la entrada de aceituna.")
     *         )
     *     )
     * )
     */
    public function showForMember($memberId, $entryId): JsonResponse
    {
        $user = Auth::user();

        // Verificar si el usuario tiene el rol 'Socio'
        if (!$user->roles->contains('name', 'Socio')) {
            return response()->json(['message' => 'No eres un Socio.'], 403);
        }

        // Obtener el miembro asociado al usuario
        $member = $user->member;

        // Verificar si el miembro está asociado al usuario y si el ID coincide con el memberId proporcionado
        if (!$member || $member->id != $memberId) {
            return response()->json(['message' => 'No tienes permiso para ver esta entrada.'], 403);
        }

        // Obtener la entrada asociada al miembro
        $entry = $member->entries()->find($entryId);

        // Verificar si la entrada existe y si pertenece al miembro
        if (!$entry || $entry->member_id != $member->id) {
            return response()->json(['message' => 'Esta entrada no pertenece al socio.'], 403);
        }

        // Si todo es correcto, devolver la entrada
        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 200);
    }
}
