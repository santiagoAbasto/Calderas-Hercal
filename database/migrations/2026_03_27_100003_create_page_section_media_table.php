<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_section_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_section_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['image', 'video', 'youtube'])->default('image');
            $table->string('file_path')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('order')->default('A');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['page_section_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_section_media');
    }
};
