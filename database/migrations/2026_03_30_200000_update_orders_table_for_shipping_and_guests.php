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

            // Add missing shipping columns safely
            if (!Schema::hasColumn('orders', 'address')) {
                $table->string('address')->after('total')->nullable();
            }
            if (!Schema::hasColumn('orders', 'city')) {
                $table->string('city')->after('address')->nullable();
            }
            if (!Schema::hasColumn('orders', 'postal_code')) {
                $table->string('postal_code')->after('city')->nullable();
            }
            if (!Schema::hasColumn('orders', 'phone')) {
                $table->string('phone')->after('postal_code')->nullable();
            }
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

            // Note: We don't drop columns here if they were added by a different migration
        });
    }
};
