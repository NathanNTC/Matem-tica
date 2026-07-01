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
                Schema::create('historico_calculos', function (Blueprint $table) {
            $table->id();
            $table->text('funcao');
            $table->double('min');
            $table->double('max');
            $table->double('passo');
            $table->double('tolerancia');

            $table->json('intervalos');
            $table->json('resultado_bissecao');
            $table->json('resultado_newton');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_calculos');
    }
};
