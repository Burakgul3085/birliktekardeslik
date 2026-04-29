<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('title_i18n')->nullable()->after('title');
            $table->json('description_i18n')->nullable()->after('description');
            $table->json('content_i18n')->nullable()->after('content');
            $table->json('detail_item_1_title_i18n')->nullable()->after('detail_item_1_title');
            $table->json('detail_item_1_text_i18n')->nullable()->after('detail_item_1_text');
            $table->json('detail_item_2_title_i18n')->nullable()->after('detail_item_2_title');
            $table->json('detail_item_2_text_i18n')->nullable()->after('detail_item_2_text');
            $table->json('detail_item_3_title_i18n')->nullable()->after('detail_item_3_title');
            $table->json('detail_item_3_text_i18n')->nullable()->after('detail_item_3_text');
        });

        DB::table('projects')->orderBy('id')->get()->each(function ($project): void {
            DB::table('projects')
                ->where('id', $project->id)
                ->update([
                    'title_i18n' => json_encode(['tr' => $project->title], JSON_UNESCAPED_UNICODE),
                    'description_i18n' => json_encode(['tr' => $project->description], JSON_UNESCAPED_UNICODE),
                    'content_i18n' => json_encode(['tr' => $project->content], JSON_UNESCAPED_UNICODE),
                    'detail_item_1_title_i18n' => json_encode(['tr' => $project->detail_item_1_title], JSON_UNESCAPED_UNICODE),
                    'detail_item_1_text_i18n' => json_encode(['tr' => $project->detail_item_1_text], JSON_UNESCAPED_UNICODE),
                    'detail_item_2_title_i18n' => json_encode(['tr' => $project->detail_item_2_title], JSON_UNESCAPED_UNICODE),
                    'detail_item_2_text_i18n' => json_encode(['tr' => $project->detail_item_2_text], JSON_UNESCAPED_UNICODE),
                    'detail_item_3_title_i18n' => json_encode(['tr' => $project->detail_item_3_title], JSON_UNESCAPED_UNICODE),
                    'detail_item_3_text_i18n' => json_encode(['tr' => $project->detail_item_3_text], JSON_UNESCAPED_UNICODE),
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'title_i18n',
                'description_i18n',
                'content_i18n',
                'detail_item_1_title_i18n',
                'detail_item_1_text_i18n',
                'detail_item_2_title_i18n',
                'detail_item_2_text_i18n',
                'detail_item_3_title_i18n',
                'detail_item_3_text_i18n',
            ]);
        });
    }
};

