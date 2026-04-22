<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('page_section_media', function (Blueprint $table) {
            $table->string('title')->nullable()->after('youtube_url');
        });
    }

    public function down(): void
    {
        Schema::table('page_section_media', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
