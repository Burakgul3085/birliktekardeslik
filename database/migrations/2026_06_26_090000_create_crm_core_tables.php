<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('staff');
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('crm_password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('donation_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('donors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('city')->nullable();
            $table->string('country')->default('Türkiye');
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['last_name', 'first_name']);
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donors')->cascadeOnDelete();
            $table->foreignId('donation_type_id')->nullable()->constrained('donation_types')->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('crm_users')->nullOnDelete();
            $table->string('donation_number')->unique();
            $table->string('receipt_number')->nullable()->unique();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 8)->default('TRY');
            $table->timestamp('donated_at');
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('donated_at');
            $table->index(['amount', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
        Schema::dropIfExists('donors');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('donation_types');
        Schema::dropIfExists('crm_password_reset_tokens');
        Schema::dropIfExists('crm_users');
    }
};
