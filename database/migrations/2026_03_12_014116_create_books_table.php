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
            $table->id();
            $table->string('book_code')->unique();
            $table->string('title');
            $table->string('author');
            $table->string('publisher');
            $table->year('publication_year');
            $table->string('isbn')->unique();
            $table->Unsignedinteger('stock');
            $table->string('cover_image');
            $table->text('description')->nullable();
            $table->string('rack_location');
            $table->timestamps();
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
