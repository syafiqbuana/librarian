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
        Schema::create('student_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nis',20)->unique();
            $table->string('class',20);
            $table->enum('major',['RPL','TJKT','AKL','TF','MPLB','PM']);
            $table->enum('gender',['Laki-laki','Perempuan']);
            $table->date('birth_date');
            $table->text('address');
            $table->string('phone',50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_details');
    }
};
