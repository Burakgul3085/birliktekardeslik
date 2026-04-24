<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('detail_item_1_title')->nullable()->after('content');
            $table->text('detail_item_1_text')->nullable()->after('detail_item_1_title');
            $table->string('detail_item_2_title')->nullable()->after('detail_item_1_text');
            $table->text('detail_item_2_text')->nullable()->after('detail_item_2_title');
            $table->string('detail_item_3_title')->nullable()->after('detail_item_2_text');
            $table->text('detail_item_3_text')->nullable()->after('detail_item_3_title');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'detail_item_1_title',
                'detail_item_1_text',
                'detail_item_2_title',
                'detail_item_2_text',
                'detail_item_3_title',
                'detail_item_3_text',
            ]);
        });
    }
};
