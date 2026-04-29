<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->json('title_i18n')->nullable()->after('title');
            $table->json('summary_i18n')->nullable()->after('summary');
            $table->json('content_i18n')->nullable()->after('content');
        });

        DB::table('news')->orderBy('id')->get()->each(function ($item): void {
            DB::table('news')
                ->where('id', $item->id)
                ->update([
                    'title_i18n' => json_encode(['tr' => $item->title], JSON_UNESCAPED_UNICODE),
                    'summary_i18n' => json_encode(['tr' => $item->summary], JSON_UNESCAPED_UNICODE),
                    'content_i18n' => json_encode(['tr' => $item->content], JSON_UNESCAPED_UNICODE),
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['title_i18n', 'summary_i18n', 'content_i18n']);
        });
    }
};

