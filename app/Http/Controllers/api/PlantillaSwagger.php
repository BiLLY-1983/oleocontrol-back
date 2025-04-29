<?php
/**
 * [Título descriptivo del método].
 * 
 * [Descripción funcional del método: qué hace, cómo lo hace, condiciones y posibles errores].
 *
 * @param [TipoDeRequest] $request [Descripción del request, por ejemplo: Datos necesarios para realizar la acción.]
 * @return JsonResponse [Descripción de la respuesta esperada.]
 * @throws [Excepción esperada si aplica, por ejemplo ValidationException].
 *
 * @OA\[MétodoHTTP](
 *     path="/api/[ruta]",
 *     summary="[Resumen breve de lo que hace el endpoint]",
 *     description="[Descripción más detallada de lo que hace el endpoint]",
 *     tags={"[Grupo o categoría, ej: Usuarios, Autenticación]"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"campo1", "campo2"},
 *             @OA\Property(property="campo1", type="tipo", example="valor"),
 *             @OA\Property(property="campo2", type="tipo", example="valor")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="[Descripción del éxito]",
 *         @OA\JsonContent(
 *             @OA\Property(property="campoRespuesta1", type="tipo", example="valor"),
 *             @OA\Property(property="campoRespuesta2", type="tipo", example="valor")
 *         )
 *     ),
 *     @OA\Response(
 *         response=4XX,
 *         description="[Mensaje de error]",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Mensaje de error")
 *         )
 *     )
 * )
 */
