<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2);
            $table->string('categoria');
            $table->string('imagen')->nullable();
            $table->boolean('disponible')->default(true);
            $table->timestamps();

            $table->unique(['nombre', 'categoria']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
