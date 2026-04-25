<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('goal_amount', 12, 2)->nullable()->after('donation_currency');
            $table->decimal('collected_amount', 12, 2)->nullable()->after('goal_amount');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['goal_amount', 'collected_amount']);
        });
    }
};
