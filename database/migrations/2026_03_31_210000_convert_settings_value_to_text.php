<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->text('value_text')->nullable()->after('value');
        });

        DB::table('settings')->orderBy('id')->eachById(function (object $setting): void {
            $value = $setting->value;
            $decoded = json_decode((string) $value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_scalar($decoded) || $decoded === null) {
                    $value = $decoded;
                } else {
                    $value = implode(', ', array_filter(array_map(static fn ($item) => is_scalar($item) ? (string) $item : null, (array) $decoded)));
                }
            }

            DB::table('settings')
                ->where('id', $setting->id)
                ->update(['value_text' => $value === null ? null : trim((string) $value)]);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('value');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('value_text', 'value');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->json('value_json')->nullable()->after('key');
        });

        DB::table('settings')->orderBy('id')->eachById(function (object $setting): void {
            DB::table('settings')
                ->where('id', $setting->id)
                ->update(['value_json' => $setting->value !== null ? json_encode($setting->value) : null]);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('value');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->renameColumn('value_json', 'value');
        });
    }
};
