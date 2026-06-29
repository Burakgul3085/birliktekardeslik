<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->text('message_template')->nullable()->after('settings');
        });

        Schema::table('donation_documents', function (Blueprint $table) {
            $table->string('status', 20)->default('draft')->after('type');
            $table->string('png_path')->nullable()->after('pdf_path');
        });

        Schema::create('document_field_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_document_id')->constrained('donation_documents')->cascadeOnDelete();
            $table->string('field_key', 64);
            $table->unsignedInteger('x')->nullable();
            $table->unsignedInteger('y')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('font_family', 64)->nullable();
            $table->unsignedInteger('font_size')->nullable();
            $table->string('color', 16)->nullable();
            $table->string('align', 16)->nullable();
            $table->string('vertical_align', 16)->nullable();
            $table->text('text_override')->nullable();
            $table->timestamps();

            $table->unique(['donation_document_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_field_overrides');

        Schema::table('donation_documents', function (Blueprint $table) {
            $table->dropColumn(['status', 'png_path']);
        });

        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn('message_template');
        });
    }
};
