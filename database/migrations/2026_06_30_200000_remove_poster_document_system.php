<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('document_field_overrides');
        Schema::dropIfExists('document_template_fields');

        if (Schema::hasColumn('donations', 'poster_name')) {
            Schema::table('donations', function (Blueprint $table) {
                $table->dropColumn('poster_name');
            });
        }

        if (Schema::hasColumn('donation_documents', 'png_path')) {
            Schema::table('donation_documents', function (Blueprint $table) {
                $table->dropColumn('png_path');
            });
        }

        if (Schema::hasColumn('donation_documents', 'status')) {
            Schema::table('donation_documents', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        $posterColumns = ['message_template', 'background_image', 'canvas_width', 'canvas_height'];
        foreach ($posterColumns as $column) {
            if (Schema::hasColumn('document_templates', $column)) {
                Schema::table('document_templates', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        DB::table('donation_documents')
            ->where('type', '!=', 'receipt')
            ->delete();

        DB::table('document_templates')
            ->where('type', '!=', 'receipt')
            ->delete();
    }

    public function down(): void
    {
        // Geri alınmaz — afiş sistemi kaldırıldı.
    }
};
