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
        Schema::create('tallas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('segmento', [
                'Adulto',
                'Infantil'
            ]);
            $table->enum('genero', [ 
                'Niño',
                'Niña',  
                'Mujer', 
                'Hombre', 
                'Unisex'
            ])->default('Unisex');

            $table->enum('categoria', ['Zapatillas Infantil', 'Zapatillas Adulto', 'Prendas Adulto', 'Prendas Infantil']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tallas');
    }
};
