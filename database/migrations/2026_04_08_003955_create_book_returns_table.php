<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained()->cascadeOnDelete();
            // Asal return
            $table->enum('source', ['student_request', 'manual_admin'])->default('student_request');

            // Kondisi buku
            $table->enum('book_condition', ['good', 'damaged', 'lost'])->default('good');
            $table->text('condition_note')->nullable();

            // Breakdown denda
            $table->integer('fine_late')->default(0);       // otomatis: hari telat × tarif
            $table->integer('fine_damaged')->default(0);    // input manual admin
            $table->integer('fine_lost')->default(0);       // input manual admin
            $table->integer('fine_total')->default(0);      // sum ketiganya

            // Status pembayaran
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();

            // Status review
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_returns');
    }
};
