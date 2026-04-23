<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->longText('kvkk_text')->nullable()->after('volunteer_preferences');
            $table->longText('volunteer_clarification_text')->nullable()->after('kvkk_text');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn(['kvkk_text', 'volunteer_clarification_text']);
        });
    }
};

