<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
  use ApiResponseTrait;
  /**
   * Registra um novo usuário
   */
  public function register(Request $request): JsonResponse
  {
    try {
      $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
      ]);

      $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
      ]);

      $token = $user->createToken('auth-token')->plainTextToken;

      return $this->successResponse([
        'user' => [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
          'created_at' => $user->created_at,
        ],
        'token' => $token,
      ], 'Usuário cadastrado com sucesso', Response::HTTP_CREATED);
    } catch (ValidationException $e) {
      return $this->validationErrorResponse($e->errors());
    } catch (\Exception $e) {
      return $this->errorResponse(
        'Erro interno do servidor', 
        $e->getMessage(), 
        Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  /**
   * Faz login do usuário
   */
  public function login(Request $request): JsonResponse
  {
    try {
      $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
      ]);

      if (!Auth::attempt($validated)) {
        return $this->unauthorizedResponse('Credenciais inválidas');
      }

      $user = Auth::user();
      $user->tokens()->delete();
      $token = $user->createToken('auth-token')->plainTextToken;

      return $this->successResponse([
        'user' => [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
        ],
        'token' => $token,
      ], 'Login realizado com sucesso');
    } catch (ValidationException $e) {
      return $this->validationErrorResponse($e->errors());
    } catch (\Exception $e) {
      return $this->errorResponse(
        'Erro interno do servidor',
        $e->getMessage(),
        Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  /**
   * Faz logout do usuário
   */
  public function logout(Request $request): JsonResponse
  {
    try {
      $request->user()->tokens()->delete();
      return $this->successResponse(null, 'Logout realizado com sucesso');
    } catch (\Exception $e) {
      return $this->errorResponse(
        'Erro ao fazer logout',
        $e->getMessage(),
        Response::HTTP_INTERNAL_SERVER_ERROR
      );
    }
  }

  /**
   * Retorna dados do usuário autenticado
   */
  public function profile(Request $request): JsonResponse
  {
    try {
      $user = $request->user();

      return $this->successResponse([
        'user' => [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
          'created_at' => $user->created_at,
          'updated_at' => $user->updated_at,
        ]
      ]);
    } catch (\Exception $e) {
      return $this->errorResponse(
        'Erro ao buscar perfil do usuário',
         $e->getMessage(), 
         Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
  }
}
