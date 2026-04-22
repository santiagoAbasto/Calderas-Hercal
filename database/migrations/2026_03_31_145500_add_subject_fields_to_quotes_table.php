<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            if (!Schema::hasColumn('quotes', 'subject_type')) {
                $table->string('subject_type')->nullable()->after('service_name');
            }

            if (!Schema::hasColumn('quotes', 'subject_slug')) {
                $table->string('subject_slug')->nullable()->after('subject_type');
            }

            if (!Schema::hasColumn('quotes', 'subject_name')) {
                $table->string('subject_name')->nullable()->after('subject_slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('quotes', 'subject_name') ? 'subject_name' : null,
                Schema::hasColumn('quotes', 'subject_slug') ? 'subject_slug' : null,
                Schema::hasColumn('quotes', 'subject_type') ? 'subject_type' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
