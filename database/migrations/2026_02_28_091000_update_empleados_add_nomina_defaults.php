<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (!Schema::hasColumn('empleados', 'horas_extra_base')) {
                $table->decimal('horas_extra_base', 8, 2)->default(0)->after('salario_base');
            }
            if (!Schema::hasColumn('empleados', 'bono_fijo')) {
                $table->decimal('bono_fijo', 12, 2)->default(0)->after('horas_extra_base');
            }
            if (!Schema::hasColumn('empleados', 'comision_fija')) {
                $table->decimal('comision_fija', 12, 2)->default(0)->after('bono_fijo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empleados', function (Blueprint $table) {
            if (Schema::hasColumn('empleados', 'horas_extra_base')) {
                $table->dropColumn('horas_extra_base');
            }
            if (Schema::hasColumn('empleados', 'bono_fijo')) {
                $table->dropColumn('bono_fijo');
            }
            if (Schema::hasColumn('empleados', 'comision_fija')) {
                $table->dropColumn('comision_fija');
            }
        });
    }
};
