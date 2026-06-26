<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exports', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('exports', function (Blueprint $table) {
            $table->string('user_type')->nullable()->after('successful_rows');
        });

        DB::table('exports')
            ->whereNotNull('user_id')
            ->whereNull('user_type')
            ->update(['user_type' => 'App\\Models\\User']);
    }

    public function down(): void
    {
        Schema::table('exports', function (Blueprint $table) {
            $table->dropColumn('user_type');
        });

        Schema::table('exports', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
