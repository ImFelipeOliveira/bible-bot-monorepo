<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BibleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/fixture/nvi.json');
        $this->command->info("Seeding Bible from {$jsonPath}");
        $data = $this->getDataFromJson($jsonPath);
        $this->seedTestaments();
        $this->seedBooks($data);
        $this->seedVerses($data);
        $this->command->info('Bible seeding completed.');
    }

    private function seedTestaments(): void
    {
        $testaments = ['Antigo Testamento', 'Novo Testamento'];
        foreach ($testaments as $testament) {
            DB::table('testament')->insert([
                'name' => $testament,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        $this->command->info('Testaments seeded.');
    }

    private function seedBooks(array $data): void
    {
        foreach ($data as $book) {
            DB::table('books')->insert([
                'name' => $book->name,
                'abbrev' => $book->abbrev,
                'testament' => $this->switchTestament($book->name),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedVerses(array $data): void
    {
        foreach ($data as $book) {
            $bookRecord = DB::table('books')->where('abbrev', $book->abbrev)->first();
            if ($bookRecord) {
                foreach ($book->chapters as $chapterNumber => $verses) {
                    foreach ($verses as $verseNumber => $text) {
                        DB::table('verses')->insert([
                            'version' => 'nvi',
                            'testament' => $this->switchTestament($book->name),
                            'book_id' => $bookRecord->id,
                            'chapter' => $chapterNumber + 1,
                            'verse' => $verseNumber + 1,
                            'text' => $text,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } else {
                $this->command->error("Book with abbrev {$book['abbrev']} not found.");
            }
        }
    }

    private function getDataFromJson(string $jsonPath): ?array
    {
        $json = file_get_contents($jsonPath);
        $json = preg_replace('/^\x{FEFF}/u', '', $json);
        $json = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $json);
        $data = json_decode($json);
        if (is_null($data)) {
            $this->command->error("Failed to decode JSON from {$jsonPath}");

            return null;
        }

        return $data;
    }

    private function switchTestament(string $book): int
    {
        $oldTestamentBooks = [
            'Gênesis', 'Êxodo', 'Levítico', 'Números', 'Deuteronômio',
            'Josué', 'Juízes', 'Rute', '1 Samuel', '2 Samuel',
            '1 Reis', '2 Reis', '1 Crônicas', '2 Crônicas', 'Esdras',
            'Neemias', 'Ester', 'Jó', 'Salmos', 'Provérbios',
            'Eclesiastes', 'Cânticos', 'Isaías', 'Jeremias', 'Lamentações',
            'Ezequiel', 'Daniel', 'Oseias', 'Joel', 'Amós',
            'Obadias', 'Jonas', 'Miquéias', 'Naum', 'Habacuque',
            'Sofonias', 'Ageu', 'Zacarias', 'Malaquias',
        ];

        return in_array($book, $oldTestamentBooks) ? 1 : 2;
    }
}
