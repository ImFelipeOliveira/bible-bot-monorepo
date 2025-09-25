<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    /**
     * Resposta de sucesso padrão
     */
    protected function successResponse(
        $data = null,
        string $message = 'Operação realizada com sucesso',
        int $status = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    /**
     * Resposta de erro padrão
     */
    protected function errorResponse(
        string $message = 'Erro interno',
        $errors = null,
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['error'] = $errors;
        }

        return response()->json($response, $status);
    }

    /**
     * Resposta de validação
     */
    protected function validationErrorResponse($errors, string $message = 'Dados inválidos'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Resposta não encontrado
     */
    protected function notFoundResponse(string $message = 'Recurso não encontrado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Resposta não autorizado
     */
    protected function unauthorizedResponse(string $message = 'Não autorizado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], Response::HTTP_UNAUTHORIZED);
    }
}
