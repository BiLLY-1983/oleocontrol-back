<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Member\StoreMemberRequest;
use App\Http\Requests\Member\UpdateMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    /**
     * Muestra todos los socios.
     * 
     * Este método obtiene todos los socios de la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos de los socios.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos de los socios.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => MemberResource::collection(Member::all())
        ], 200);
    }

    /**
     * Crea un nuevo socio.
     * 
     * Este método recibe una solicitud de creación de socio, valida los datos y crea un nuevo socio en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del socio creado en formato JSON.
     *
     * @param StoreMemberRequest $request La solicitud de creación de socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio creado.
     */
    public function store(StoreMemberRequest $request): JsonResponse
    {
        $member = Member::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 201);
    }

    /**
     * Muestra un socio específico por su ID.
     * 
     * Este método recibe un ID de socio, busca el socio en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del socio.
     *
     * @param int $id El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio.
     */
    public function show($id): JsonResponse
    {
        $member = Member::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }

    /**
     * Actualiza un socio específico por su ID.
     * 
     * Este método recibe una solicitud de actualización de socio, valida los datos y actualiza el socio en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del socio actualizado en formato JSON.
     *
     * @param UpdateMemberRequest $request La solicitud de actualización de socio.
     * @param int $id El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio actualizado.
     */
    public function update(UpdateMemberRequest $request, $id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $member->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }

    /**
     * Elimina un socio específico por su ID.
     * 
     * Este método recibe un ID de socio, busca el socio en la base de datos y elimina el socio.
     * La respuesta incluye un estado de éxito y un mensaje de éxito en formato JSON.
     *
     * @param int $id El ID del socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y un mensaje de éxito.
     */
    public function destroy($id): JsonResponse
    {
        $member = Member::findOrFail($id);
        $member->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Socio eliminado satisfactoriamente'
        ], 200);
    }

    /**
     * Muestra el perfil del socio autenticado.
     * 
     * Este método obtiene el socio autenticado, busca el socio en la base de datos y devuelve una respuesta JSON con un estado de éxito y los datos del socio.
     *
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio.
     */
    public function showProfile(): JsonResponse
    {
        $member = Auth::user()->member;

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }

    /**
     * Actualiza el perfil del socio autenticado.
     * 
     * Este método recibe una solicitud de actualización de socio, valida los datos y actualiza el socio en la base de datos.
     * La respuesta incluye un estado de éxito y los datos del socio actualizado en formato JSON.
     *
     * @param UpdateMemberRequest $request La solicitud de actualización de socio.
     * @return JsonResponse Respuesta JSON con el estado de éxito y los datos del socio actualizado.
     */
    public function updateProfile(UpdateMemberRequest $request): JsonResponse
    {
        $member = Auth::user()->member;
        $member->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new MemberResource($member)
        ], 200);
    }
}
