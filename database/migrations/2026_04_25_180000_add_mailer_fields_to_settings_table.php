<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->string('mailer_host')->nullable()->after('telegram_url');
            $table->unsignedSmallInteger('mailer_port')->nullable()->after('mailer_host');
            $table->string('mailer_encryption', 10)->nullable()->after('mailer_port');
            $table->string('mailer_username')->nullable()->after('mailer_encryption');
            $table->text('mailer_password')->nullable()->after('mailer_username');
            $table->string('mailer_from_address')->nullable()->after('mailer_password');
            $table->string('mailer_from_name')->nullable()->after('mailer_from_address');
            $table->string('mailer_notification_email')->nullable()->after('mailer_from_name');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table): void {
            $table->dropColumn([
                'mailer_host',
                'mailer_port',
                'mailer_encryption',
                'mailer_username',
                'mailer_password',
                'mailer_from_address',
                'mailer_from_name',
                'mailer_notification_email',
            ]);
        });
    }
};
