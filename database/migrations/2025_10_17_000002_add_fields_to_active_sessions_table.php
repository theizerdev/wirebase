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
        Schema::table('active_sessions', function (Blueprint $table) {
            // Añadir campos para hacerla más útil como historial
            $table->timestamp('login_at')->nullable()->after('last_activity');
            $table->timestamp('logout_at')->nullable()->after('login_at');
            $table->string('location')->nullable()->after('user_agent');
            $table->boolean('is_active')->default(true)->after('is_current');
            // Añadir campos de geolocalización
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('active_sessions', function (Blueprint $table) {
            $table->dropColumn(['login_at', 'logout_at', 'location', 'is_active', 'latitude', 'longitude']);
        });
    }
};