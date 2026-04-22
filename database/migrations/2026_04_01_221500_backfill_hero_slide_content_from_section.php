<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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
        $currentTitle = trim((string) ($firstSlide->title ?? ''));

        DB::table('page_section_media')
            ->where('id', $firstSlide->id)
            ->update([
                'title' => $currentTitle === '' || $currentTitle === 'Slide principal'
                    ? $heroSection->title
                    : $firstSlide->title,
                'description' => $firstSlide->description ?: $heroSection->description,
                'button_text' => $firstSlide->button_text ?: ($meta['button_text'] ?? null),
                'button_url' => $firstSlide->button_url ?: ($meta['button_url'] ?? null),
            ]);
    }

    public function down(): void
    {
    }
};
