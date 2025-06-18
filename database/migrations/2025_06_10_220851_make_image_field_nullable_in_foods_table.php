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
        Schema::table('foods', function (Blueprint $table) {
            // Mengubah kolom 'image' (yang bertipe varchar) menjadi NULLABLE
            $table->string('image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            // Mengembalikan kolom 'image' menjadi NOT NULL
            // Perlu diingat: ini akan gagal jika ada NULL di kolom 'image'
            // Anda mungkin perlu memberikan nilai default, e.g., ->default('')
            $table->string('image')->nullable(false)->change();
        });
    }
};