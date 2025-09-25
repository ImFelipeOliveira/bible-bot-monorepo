<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
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

      return response()->json([
        'success' => true,
        'message' => 'Usuário cadastrado com sucesso',
        'data' => [
          'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
          ],
          'token' => $token,
        ]
      ], Response::HTTP_CREATED);
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Dados inválidos',
        'errors' => $e->errors()
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro interno do servidor',
        'error' => $e->getMessage()
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
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
        return response()->json([
          'success' => false,
          'message' => 'Credenciais inválidas'
        ], Response::HTTP_UNAUTHORIZED);
      }

      $user = Auth::user();

      $user->tokens()->delete();

      $token = $user->createToken('auth-token')->plainTextToken;

      return response()->json([
        'success' => true,
        'message' => 'Login realizado com sucesso',
        'data' => [
          'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
          ],
          'token' => $token,
        ]
      ], Response::HTTP_OK);
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Dados inválidos',
        'errors' => $e->errors()
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro interno do servidor',
        'error' => $e->getMessage()
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Faz logout do usuário
   */
  public function logout(Request $request): JsonResponse
  {
    try {
      $request->user()->tokens()->delete();

      return response()->json([
        'success' => true,
        'message' => 'Logout realizado com sucesso'
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro ao fazer logout',
        'error' => $e->getMessage()
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  /**
   * Retorna dados do usuário autenticado
   */
  public function profile(Request $request): JsonResponse
  {
    try {
      $user = $request->user();

      return response()->json([
        'success' => true,
        'data' => [
          'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
          ]
        ]
      ], Response::HTTP_OK);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erro ao buscar perfil do usuário',
        'error' => $e->getMessage()
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
