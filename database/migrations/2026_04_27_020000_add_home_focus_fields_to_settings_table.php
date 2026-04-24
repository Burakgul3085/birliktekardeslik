<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('home_focus_1_title')->nullable()->after('privacy_policy_text');
            $table->text('home_focus_1_text')->nullable()->after('home_focus_1_title');
            $table->string('home_focus_2_title')->nullable()->after('home_focus_1_text');
            $table->text('home_focus_2_text')->nullable()->after('home_focus_2_title');
            $table->string('home_focus_3_title')->nullable()->after('home_focus_2_text');
            $table->text('home_focus_3_text')->nullable()->after('home_focus_3_title');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_focus_1_title',
                'home_focus_1_text',
                'home_focus_2_title',
                'home_focus_2_text',
                'home_focus_3_title',
                'home_focus_3_text',
            ]);
        });
    }
};
