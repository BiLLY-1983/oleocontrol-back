<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entry\StoreEntryRequest;
use App\Http\Requests\Entry\UpdateEntryRequest;
use App\Http\Resources\EntryResource;
use App\Models\Entry;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EntryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => EntryResource::collection(Entry::all())
        ], 200);
    }

    public function store(StoreEntryRequest $request): JsonResponse
    {
        $entry = Entry::create($request->validated());
        
        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 201);
    }

    public function show($id): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new EntryResource(Entry::findOrFail($id))
        ], 200);
    }

    public function update(UpdateEntryRequest $request, $id): JsonResponse
    {
        $entry = Entry::findOrFail($id);
        $entry->update($request->validated());

        return response()->json([
            'status' => 'success',
            'data' => new EntryResource($entry)
        ], 200);
    }

    public function destroy($id): JsonResponse
    {
        $entry = Entry::findOrFail($id);
        $entry->delete();

        return response()->json([
            'status' => 'success',
        ], 200);
    }

    /**
     *  Función que devuelve todas las entradas de un Socio
     * 
     *  @param int ID del socio del que se desean obtener las entradas de aceituna
     *  @return JsonResponse Respuesta JSON con un mensaje de éxito y los datos de las entradas del socio o un mensaje de error si no tiene permisos
     * 
     *  Nota: El ID recibido es el ID del socio (tabla Member), no del id del usuario (Tabla User).
     */
    public function getEntriesUser($memberId): JsonResponse
    {        
        // Verificar si el usuario autenticado es un administrador
        if (Auth::user()->roles->contains('name', 'Administrador')) {

            // Almacena las entradas del id del socio recibido como parámetro
            $entries = Entry::where('member_id', $memberId)->get();
            
        // Comprobación del id del usuario autenticado y el id del socio recibido
        } elseif (Auth::user()->members->user_id === (int)$memberId) {

            // Almacena las entradas del id del socio autenticado (NO del id del usuario)
            $entries = Entry::where('member_id', Auth::user()->members->id)->get();
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No tienes permisos para ver las entradas de este usuario.'
            ], 403);
        }

        // Verificar si existen entradas de aceituna
        if ($entries->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se han encontrado entradas'
            ], 404);
        }

        // Respuesta del recurso
        return response()->json([
            'status' => 'success',
            'data' => EntryResource::collection($entries)
        ], 200);
    }
}
