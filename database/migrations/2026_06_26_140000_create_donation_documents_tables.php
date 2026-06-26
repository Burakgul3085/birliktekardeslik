<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('blade_view');
            $table->string('background_image')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        Schema::create('donation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->cascadeOnDelete();
            $table->foreignId('document_template_id')->nullable()->constrained('document_templates')->nullOnDelete();
            $table->string('type');
            $table->string('verification_code', 64)->unique();
            $table->string('pdf_path');
            $table->timestamp('generated_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['donation_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_documents');
        Schema::dropIfExists('document_templates');
    }
};
