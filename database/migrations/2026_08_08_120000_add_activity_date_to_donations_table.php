<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Bağışın sahada faaliyete geçirildiği tarih, bağışın alındığı tarihten
 * farklı olabildiği için ayrı bir alanda tutulur. Afişlerde bu tarih kullanılır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->date('activity_date')->nullable()->after('donated_at')->index();
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex(['activity_date']);
            $table->dropColumn('activity_date');
        });
    }
};
