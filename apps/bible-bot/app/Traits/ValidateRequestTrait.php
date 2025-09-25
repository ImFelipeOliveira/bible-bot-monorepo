<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait ValidateRequestTrait
{
    /**
     * Valida os dados da requisição com as regras fornecidas
     */
    protected function validateRequest(Request $request, array $fields, array $customMessages = []): array
    {
        $rules = $this->mapFieldsToRules($fields);
        return $request->validate($rules, $customMessages);
    }

    /**
     * Mapeia nomes de campos para suas regras de validação
     */
    private function mapFieldsToRules(array $fields): array
    {
        $RULES_MAP = [
            // Autenticação
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'email_login' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'password_login' => 'required|string',
            'current_password' => 'required|string',
            
            // Bíblia/Versículos
            'book_name' => 'required|string|max:100',
            'chapter' => 'required|integer|min:1',
            'verse' => 'required|integer|min:1',
            'text' => 'required|string',
            'version' => 'nullable|string|max:50',
            
            // Favoritos/Notas
            'verse_id' => 'required|integer|exists:verses,id',
            'note' => 'nullable|string|max:1000',
            'is_favorite' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            
            // Configurações gerais
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,pending',
            'category' => 'nullable|string|max:100',
            'priority' => 'nullable|integer|min:1|max:5',
            
            // Campos comuns
            'id' => 'required|integer|exists:users,id',
            'user_id' => 'required|integer|exists:users,id',
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'order_by' => 'nullable|string|in:id,name,created_at,updated_at',
            'direction' => 'nullable|string|in:asc,desc',
        ];

        $mappedRules = [];
        foreach ($fields as $field) {
            if (isset($RULES_MAP[$field])) {
                $mappedRules[$field] = $RULES_MAP[$field];
            } else {
                // Lança exceção para campo não mapeado (mais seguro)
                throw new \InvalidArgumentException("Campo de validação '{$field}' não está mapeado no ValidateRequestTrait");
            }
        }
        
        return $mappedRules;
    }

    /**
     * Valida com regras customizadas (modo tradicional)
     */
    protected function validateWithRules(Request $request, array $rules, array $messages = []): array
    {
        return $request->validate($rules, $messages);
    }

    /**
     * Métodos de conveniência para validações comuns
     */
    protected function validateAuth(Request $request): array
    {
        return $this->validateRequest($request, ['name', 'email', 'password']);
    }

    protected function validateLogin(Request $request): array
    {
        return $this->validateRequest($request, ['email_login', 'password_login']);
    }

    protected function validateVerse(Request $request): array
    {
        return $this->validateRequest($request, ['book_name', 'chapter', 'verse', 'text', 'version']);
    }

    protected function validateSearch(Request $request): array
    {
        return $this->validateRequest($request, ['search', 'page', 'per_page', 'order_by', 'direction']);
    }
}
