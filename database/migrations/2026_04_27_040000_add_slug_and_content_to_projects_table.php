<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->longText('content')->nullable()->after('description');
        });

        DB::table('projects')->orderBy('id')->get()->each(function ($project) {
            $base = Str::slug((string) $project->title) ?: 'faaliyet';
            $slug = $base;
            $counter = 2;

            while (DB::table('projects')->where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
                $slug = $base.'-'.$counter;
                $counter++;
            }

            DB::table('projects')->where('id', $project->id)->update([
                'slug' => $slug,
                'content' => $project->description,
            ]);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'content']);
        });
    }
};
