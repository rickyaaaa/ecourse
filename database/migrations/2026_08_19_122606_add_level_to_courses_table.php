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
        Schema::table('courses', function (Blueprint $table) {
            // Nullable: kursus lama tanpa level tetap tampil wajar di
            // katalog (view sudah menjaga dengan @if(!empty($course['level']))).
            $table->enum('level', ['Pemula', 'Menengah', 'Lanjutan', 'Semua Level'])
                ->nullable()
                ->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
