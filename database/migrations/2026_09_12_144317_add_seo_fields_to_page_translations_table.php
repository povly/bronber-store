<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('page_translations', function (Blueprint $table): void {
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('meta_robots', 60)->default('index, follow')->after('meta_keywords');
            $table->string('canonical_url')->nullable()->after('meta_robots');
            $table->string('og_image')->nullable()->after('canonical_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_translations', function (Blueprint $table): void {
            $table->dropColumn(['meta_keywords', 'meta_robots', 'canonical_url', 'og_image']);
        });
    }
};
