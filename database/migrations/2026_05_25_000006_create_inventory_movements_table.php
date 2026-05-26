<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->cascadeOnDelete();
            $table->enum('type', ['entrada', 'salida', 'ajuste']);
            $table->decimal('quantity', 10, 3);
            $table->decimal('before_stock', 10, 3);
            $table->decimal('after_stock', 10, 3);
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable(); // 'order', 'purchase_order', 'manual'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('created_by')->nullable()->nullOnDelete()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
