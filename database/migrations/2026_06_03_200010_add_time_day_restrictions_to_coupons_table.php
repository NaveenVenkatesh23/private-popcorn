<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->json('valid_days')->nullable()->after('is_active'); // ["wednesday", "thursday"]
            $table->time('valid_from_time')->nullable()->after('valid_days'); // 11:00:00
            $table->time('valid_until_time')->nullable()->after('valid_from_time'); // 17:00:00
        });
    }

    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['valid_days', 'valid_from_time', 'valid_until_time']);
        });
    }
    
};
