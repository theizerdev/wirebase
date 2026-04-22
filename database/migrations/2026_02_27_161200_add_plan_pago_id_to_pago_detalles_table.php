<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pago_detalles', 'plan_pago_id')) {
            Schema::table('pago_detalles', function (Blueprint $table) {
                $table->foreignId('plan_pago_id')->nullable()->after('concepto_pago_id')->constrained('plan_pagos')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pago_detalles', 'plan_pago_id')) {
            Schema::table('pago_detalles', function (Blueprint $table) {
                $table->dropForeign(['plan_pago_id']);
                $table->dropColumn('plan_pago_id');
            });
        }
    }
};
