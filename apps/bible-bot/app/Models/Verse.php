<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

class Verse extends Model
{
    protected $table = "verses";

    protected $primaryKey = "id";

    public static function getVersesByReference(string $reference = ""): string
    {
        $pattern = '/^([1-3]?\s?[A-Za-zÀ-ÖØ-öø-ÿ]+)\s+(\d+):(\d+)$/u';
        if (preg_match($pattern, $reference, $matches)) {
            $bookName = trim($matches[1]);
            $chapter = (int) $matches[2];
            $verse = (int) $matches[3];

            $book = Book::query()->where('name', 'LIKE', $bookName)
                ->orWhere('abbreviation', 'LIKE', $bookName)
                ->first();

            if (!$book) {
                return "Livro não encontrado: {$bookName}";
            }

            $verseData = self::query()->where('book_id', $book->id)
                ->where('chapter', $chapter)
                ->where('verse', $verse)
                ->first();

            if (!$verseData) {
                return "Verso não encontrado: {$reference}";
            }

            return $verseData->text;
        }

        return "Referência inválida: {$reference}";
    }
}
