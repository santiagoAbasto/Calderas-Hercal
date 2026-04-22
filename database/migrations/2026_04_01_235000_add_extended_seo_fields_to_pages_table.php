<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('canonical_url', 2048)->nullable()->after('meta_keywords');
            $table->string('robots', 50)->nullable()->after('canonical_url');
            $table->string('og_image')->nullable()->after('robots');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['canonical_url', 'robots', 'og_image']);
        });
    }
};
