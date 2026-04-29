<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('image_path_tablet')->nullable()->after('image_path');
            $table->string('image_path_mobile')->nullable()->after('image_path_tablet');
        });
    }

    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['image_path_tablet', 'image_path_mobile']);
        });
    }
};

