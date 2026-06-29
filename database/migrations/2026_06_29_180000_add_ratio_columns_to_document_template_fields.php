<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_template_fields', function (Blueprint $table) {
            $table->decimal('x_ratio', 10, 6)->nullable()->after('height');
            $table->decimal('y_ratio', 10, 6)->nullable()->after('x_ratio');
            $table->decimal('width_ratio', 10, 6)->nullable()->after('y_ratio');
            $table->decimal('height_ratio', 10, 6)->nullable()->after('width_ratio');
        });
    }

    public function down(): void
    {
        Schema::table('document_template_fields', function (Blueprint $table) {
            $table->dropColumn(['x_ratio', 'y_ratio', 'width_ratio', 'height_ratio']);
        });
    }
};
