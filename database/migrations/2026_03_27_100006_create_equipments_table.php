<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_category_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('main_image')->nullable();
            $table->json('features')->nullable();
            $table->string('order')->default('A');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['equipment_category_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};
