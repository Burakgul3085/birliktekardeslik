<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zakat_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('nisap_grams', 8, 2)->default(80);
            $table->unsignedTinyInteger('nisap_karat')->default(24);
            $table->decimal('rate', 8, 4)->default(0.025);
            $table->json('intro_i18n')->nullable();
            $table->json('legal_text_i18n')->nullable();
            $table->json('faq_i18n')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zakat_settings');
    }
};
