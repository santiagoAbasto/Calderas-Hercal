<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $this->normalizeOrders('page_sections');
        $this->normalizeOrders('page_section_media');
        $this->seedHomeHeroMedia();
    }

    public function down(): void
    {
        //
    }

    protected function normalizeOrders(string $table): void
    {
        $rows = DB::table($table)
            ->select('id', 'order')
            ->orderBy('id')
            ->get();

        foreach ($rows as $row) {
            $order = strtoupper(trim((string) ($row->order ?? '')));

            if ($order !== '' && ! ctype_digit($order)) {
                continue;
            }

            $numeric = (int) $order;
            $normalized = $numeric > 0
                ? $this->numberToAlpha($numeric)
                : 'A';

            DB::table($table)
                ->where('id', $row->id)
                ->update(['order' => $normalized]);
        }
    }

    protected function seedHomeHeroMedia(): void
    {
        $page = DB::table('pages')->where('slug', 'home')->first();

        if (! $page) {
            return;
        }

        $heroSection = DB::table('page_sections')
            ->where('page_id', $page->id)
            ->where('section_key', 'hero')
            ->first();

        if (! $heroSection) {
            return;
        }

        $hasMedia = DB::table('page_section_media')
            ->where('page_section_id', $heroSection->id)
            ->exists();

        if ($hasMedia || ! Storage::disk('public')->exists('pages/home.png')) {
            return;
        }

        DB::table('page_section_media')->insert([
            'page_section_id' => $heroSection->id,
            'type' => 'image',
            'file_path' => 'pages/home.png',
            'youtube_url' => null,
            'title' => 'Slide principal',
            'alt_text' => 'Calderas Hercal',
            'order' => 'A',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function numberToAlpha(int $number): string
    {
        $result = '';

        while ($number > 0) {
            $number--;
            $result = chr(65 + ($number % 26)) . $result;
            $number = intdiv($number, 26);
        }

        return $result ?: 'A';
    }
};
