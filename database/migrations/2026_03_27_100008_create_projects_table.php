<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('order')->default('A');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
