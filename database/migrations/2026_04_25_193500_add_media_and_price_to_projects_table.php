<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('donation_amount', 12, 2)->nullable()->after('description');
            $table->string('donation_currency', 10)->default('TL')->after('donation_amount');
            $table->json('gallery_images')->nullable()->after('cover_image');
            $table->json('gallery_videos')->nullable()->after('gallery_images');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'donation_amount',
                'donation_currency',
                'gallery_images',
                'gallery_videos',
            ]);
        });
    }
};
