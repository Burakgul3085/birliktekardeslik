<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table): void {
            $table->string('qr_image')->nullable()->after('account_number');
        });

        Schema::table('settings', function (Blueprint $table): void {
            $table->string('google_maps_embed_url')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table): void {
            $table->dropColumn('qr_image');
        });

        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn('google_maps_embed_url');
        });
    }
};
