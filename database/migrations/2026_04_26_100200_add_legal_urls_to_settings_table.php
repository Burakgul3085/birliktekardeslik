<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->string('legal_kvkk_url')->nullable();
            $table->string('legal_privacy_url')->nullable();
            $table->string('legal_terms_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn(['legal_kvkk_url', 'legal_privacy_url', 'legal_terms_url']);
        });
    }
};
