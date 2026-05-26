<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->enum('estado', ['disponible', 'ocupada', 'reservada', 'limpieza'])
                  ->default('disponible')
                  ->after('activa');
            $table->string('ubicacion')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropColumn(['estado', 'ubicacion']);
        });
    }
};
