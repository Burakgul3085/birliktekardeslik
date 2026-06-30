<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poster_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // donation_poster | thanks_poster
            $table->string('background_path')->nullable();
            $table->unsignedInteger('canvas_width')->default(0);
            $table->unsignedInteger('canvas_height')->default(0);
            $table->json('layout')->nullable();
            $table->text('thanks_text_template')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        Schema::create('poster_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->cascadeOnDelete();
            $table->foreignId('poster_template_id')->nullable()->constrained('poster_templates')->nullOnDelete();
            $table->string('type'); // donation_poster | thanks_poster
            $table->string('image_path');
            $table->string('pdf_path')->nullable();
            $table->json('layout_snapshot')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['donation_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('poster_documents');
        Schema::dropIfExists('poster_templates');
    }
};
