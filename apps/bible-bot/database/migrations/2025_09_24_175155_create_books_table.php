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
        Schema::create('books', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name', 100);
            $table->string('abbrev', 10)->unique();
            $table->unsignedSmallInteger('testament');
            $table->timestamps();

            $table->foreign('testament')->references('id')->on('testament')->onDelete('set null');
            $table->index('testament');
            $table->index('abbrev');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
