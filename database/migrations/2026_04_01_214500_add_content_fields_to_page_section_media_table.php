<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_section_media', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('button_text')->nullable()->after('description');
            $table->string('button_url', 2048)->nullable()->after('button_text');
        });

        $heroSection = DB::table('page_sections')->where('section_key', 'hero')->first();

        if (! $heroSection) {
            return;
        }

        $firstSlide = DB::table('page_section_media')
            ->where('page_section_id', $heroSection->id)
            ->orderBy('order')
            ->orderBy('id')
            ->first();

        if (! $firstSlide) {
            return;
        }

        $meta = json_decode((string) ($heroSection->meta ?? '{}'), true) ?: [];

        DB::table('page_section_media')
            ->where('id', $firstSlide->id)
            ->update([
                'title' => $firstSlide->title ?: $heroSection->title,
                'description' => $heroSection->description,
                'button_text' => $meta['button_text'] ?? null,
                'button_url' => $meta['button_url'] ?? null,
            ]);
    }

    public function down(): void
    {
        Schema::table('page_section_media', function (Blueprint $table) {
            $table->dropColumn(['description', 'button_text', 'button_url']);
        });
    }
};
