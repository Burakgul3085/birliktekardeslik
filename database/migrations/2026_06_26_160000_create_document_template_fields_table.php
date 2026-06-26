<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->unsignedInteger('canvas_width')->nullable()->after('background_image');
            $table->unsignedInteger('canvas_height')->nullable()->after('canvas_width');
        });

        Schema::create('document_template_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_template_id')->constrained('document_templates')->cascadeOnDelete();
            $table->string('field_key', 64);
            $table->string('label', 120);
            $table->string('field_type', 20)->default('text');
            $table->unsignedInteger('x')->default(0);
            $table->unsignedInteger('y')->default(0);
            $table->unsignedInteger('width')->default(100);
            $table->unsignedInteger('height')->default(50);
            $table->string('font_family', 64)->default('DejaVuSans');
            $table->unsignedInteger('font_size')->default(32);
            $table->string('color', 16)->default('#1B3A6B');
            $table->string('align', 16)->default('center');
            $table->string('vertical_align', 16)->default('middle');
            $table->unsignedTinyInteger('max_lines')->default(5);
            $table->boolean('auto_resize')->default(true);
            $table->boolean('word_wrap')->default(true);
            $table->decimal('line_height', 4, 2)->default(1.40);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['document_template_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_template_fields');

        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['canvas_width', 'canvas_height']);
        });
    }
};
