<?php

namespace App\services;

use App\Models\Verse;

class CommandHandlerService
{

    public function handleMessage(string $message): string | null
    {
        $message = trim($message);
        if (startsWith($message, "/")) {
            [$command, $args] = $this->parseCommand($message);
            switch ($command) {
                case "versiculo":
                case "v":
                    return $this->handleVersiculoCommand($args);
                case "estudo":
                case "est":
                    return $this->handleEstudoCommand($args);
                case "help":
                case "ajuda":
                    return $this->helpMessage();
                default:
                    return $this->unknownCommandMessage($command);
            }
        }

        return null;
    }
    /**
     * Handles the /versiculo or /v command.
     * Expects $args in the format "Book Chapter:Verse", e.g., "Mateus 8:1-10".
     */
    public function handleVersiculoCommand(string $args): string
    {
        [$book, $ref] = explode(' ', $args, 2);
        $verses = Verse::getVersesByReference("{$book} {$ref}");
        return $verses;
    }

    public function handleEstudoCommand(string $args): string
    {
        // Implementação futura para o comando /estudo
        return "Comando /estudo ainda não implementado.";
    }
    /**
     * Returns the help message listing available commands.
     */
    public function helpMessage(): string
    {
        return "Comandos disponíveis:\n" .
            "/versiculo ou /v [livro capítulo:versículo] - Retorna o versículo especificado.\n" .
            "/estudo ou /est [tema] - Retorna um estudo bíblico sobre o tema especificado.\n" .
            "/help ou /ajuda - Mostra esta mensagem de ajuda.";
    }
    /**
     * Returns a message for unknown commands.
     */
    public function unknownCommandMessage(string $command): string
    {
        return "Comando desconhecido: /{$command}. Use /help para ver os comandos disponíveis.";
    }
    /**
     * Parses a command message into its components.
     */
    private function parseCommand(string $message): array
    {
        $withoutSlash = preg_replace('/^\s*\/+\s*/u', '', $message);
        $parts = preg_split('/\s+/', $withoutSlash, 2);
        $rawCommand = $parts[0] ?? '';
        $command = mb_strtolower(trim(preg_replace('/[^\p{L}\p{N}]/u', '', $rawCommand)));
        $args = isset($parts[1]) ? trim($parts[1]) : '';
        return [$command, $args];
    }
}
