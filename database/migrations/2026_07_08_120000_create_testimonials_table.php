<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('display_name');
            $table->string('city');
            $table->string('email');
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_volunteer')->default(false);
            $table->boolean('is_donor')->default(false);
            $table->string('status')->default('pending');
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
