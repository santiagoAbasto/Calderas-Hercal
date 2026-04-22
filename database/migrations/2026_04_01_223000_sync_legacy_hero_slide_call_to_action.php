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

        $meta = json_decode((string) ($heroSection->meta ?? '{}'), true) ?: [];
        $buttonText = $meta['button_text'] ?? null;
        $buttonUrl = $meta['button_url'] ?? null;

        if (! $buttonText && ! $buttonUrl) {
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

        $currentTitle = trim(strip_tags((string) ($firstSlide->title ?? '')));
        $heroTitle = trim(strip_tags((string) ($heroSection->title ?? '')));

        if ($currentTitle !== $heroTitle && $currentTitle !== 'Slide principal') {
            return;
        }

        DB::table('page_section_media')
            ->where('id', $firstSlide->id)
            ->update([
                'button_text' => $buttonText,
                'button_url' => $buttonUrl,
                'description' => $heroSection->description,
            ]);
    }

    public function down(): void
    {
    }
};
