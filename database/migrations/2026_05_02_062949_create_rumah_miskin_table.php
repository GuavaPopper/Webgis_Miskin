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
        Schema::create('rumah_miskin', function (Blueprint $table) {
            $table->string('id_rumah', 50)->primary();
            $table->text('alamat')->nullable();
            $table->integer('jumlah_kk')->default(1);
            $table->integer('jumlah_orang')->default(1);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rumah_miskin');
    }
};
