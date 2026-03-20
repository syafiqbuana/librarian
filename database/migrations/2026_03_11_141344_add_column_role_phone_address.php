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
        Schema::table('users', function (Blueprint $table) {
            $table ->enum('role',['admin','student'])->after('password');
            $table ->string('phone',50)->after('role');
            $table->text('address')->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
                $table->dropColumn('phone');
                $table->dropColumn('address');
        });
    }
};
