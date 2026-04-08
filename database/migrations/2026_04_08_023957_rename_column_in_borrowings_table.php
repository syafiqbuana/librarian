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
        Schema::table('borrowings', function (Blueprint $table) {
            $table->renameColumn('name', 'walk_in_name');           // rename kolom lama
            $table->string('walk_in_nis')->nullable()->after('walk_in_name'); // tambah baru
            $table->enum('source', ['student_request', 'manual_admin'])
                ->default('student_request')->after('status');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            //
        });
    }
};
