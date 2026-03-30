<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Support guest checkout by making user_id nullable
            $table->foreignId('user_id')->nullable()->change();

            // Add missing shipping columns
            $table->string('address')->after('total');
            $table->string('city')->after('address');
            $table->string('postal_code')->after('city');
            $table->string('phone')->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Rollback nullable change (not recommended if data is present)
            $table->foreignId('user_id')->nullable(false)->change();

            // Remove columns
            $table->dropColumn(['address', 'city', 'postal_code', 'phone']);
        });
    }
};
