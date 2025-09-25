<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verses', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('version', 20)->default('nvi');
            $table->tinyInteger('testament')->nullable();
            $table->unsignedBigInteger('book_id')->nullable();
            $table->integer('chapter')->unsigned();
            $table->integer('verse')->unsigned();
            $table->text('text');
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('set null');
            $table->unique(['book_id', 'chapter', 'verse', 'version'], 'verses_book_chapter_verse_version');
            $table->index(['book_id', 'chapter']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verses');
    }
};
