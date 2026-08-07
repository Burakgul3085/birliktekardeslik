<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Aynı telefon veya e-posta ile birden fazla bağışçı kaydı açılabilmesi için
 * tekillik kısıtları kaldırılır. Arama performansı normal indekslerle korunur.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropUnique(['email']);

            $table->index('phone');
            $table->index('email');
        });
    }

    public function down(): void
    {
        /*
         * Geri alma yalnızca mükerrer kayıt bulunmadığında çalışır.
         * Mükerrer kayıt varsa önce temizlenmesi gerekir.
         */
        Schema::table('donors', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['email']);

            $table->unique('phone');
            $table->unique('email');
        });
    }
};
