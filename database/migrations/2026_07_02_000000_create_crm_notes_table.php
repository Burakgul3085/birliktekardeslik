<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crm_user_id')->constrained('crm_users')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('body');
            $table->string('scope', 20)->default('general');
            $table->foreignId('donor_id')->nullable()->constrained('donors')->cascadeOnDelete();
            $table->foreignId('donation_id')->nullable()->constrained('donations')->cascadeOnDelete();
            $table->string('category', 30)->default('other');
            $table->boolean('is_pinned')->default(false);
            $table->string('visibility', 20)->default('team');
            $table->timestamps();

            $table->index(['scope', 'is_pinned', 'created_at']);
            $table->index(['donor_id', 'created_at']);
            $table->index(['donation_id', 'created_at']);
            $table->index(['crm_user_id', 'visibility']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_notes');
    }
};
