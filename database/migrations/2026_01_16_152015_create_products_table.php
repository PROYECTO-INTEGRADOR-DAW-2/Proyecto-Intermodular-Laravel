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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('marca');
            $table->string('categoria');
            $table->string('nombre')->unique();
            $table->float('precio');
            $table->string('talla');
            $table->string('color');
            $table->integer('stock');
            $table->string('ajuste');
            $table->string('sexo');
            $table->string('descripcion');
            $table->string('altura');
            $table->string('deporte');
            $table->boolean('oferta');
            $table->string('img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
