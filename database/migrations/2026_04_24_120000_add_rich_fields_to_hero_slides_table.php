<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('kicker')->nullable()->after('headline');
            $table->string('accent_text')->nullable()->after('kicker');
            $table->string('background_image_path')->nullable()->after('image_path');
            $table->string('thumbnail_image_path')->nullable()->after('background_image_path');
            $table->boolean('show_site_logo')->default(true)->after('button_url');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn([
                'kicker',
                'accent_text',
                'background_image_path',
                'thumbnail_image_path',
                'show_site_logo',
            ]);
        });
    }
};
