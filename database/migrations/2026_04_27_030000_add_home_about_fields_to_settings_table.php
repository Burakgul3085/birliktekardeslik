<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('home_about_badge')->nullable()->after('home_focus_3_text');
            $table->string('home_about_title')->nullable()->after('home_about_badge');
            $table->text('home_about_intro')->nullable()->after('home_about_title');
            $table->longText('home_about_body')->nullable()->after('home_about_intro');
            $table->text('home_about_items')->nullable()->after('home_about_body');
            $table->string('home_about_button_text')->nullable()->after('home_about_items');
            $table->string('home_about_image')->nullable()->after('home_about_button_text');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_about_badge',
                'home_about_title',
                'home_about_intro',
                'home_about_body',
                'home_about_items',
                'home_about_button_text',
                'home_about_image',
            ]);
        });
    }
};
