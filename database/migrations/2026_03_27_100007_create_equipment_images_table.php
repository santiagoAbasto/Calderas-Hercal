<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')
                ->constrained('equipments')
                ->cascadeOnDelete();
            $table->string('file_path');
            $table->string('order')->default('A');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['equipment_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_images');
    }
};
