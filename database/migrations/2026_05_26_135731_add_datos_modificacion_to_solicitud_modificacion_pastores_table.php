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
        Schema::table('solicitud_modificacion_pastores', function (Blueprint $table) {
            // Cambiar telefono_nuevo por datos_completos (JSON)
            $table->dropColumn('telefono_nuevo');
            
            // Almacenar todos los datos que el pastor quiere modificar
            $table->json('datos_solicitados')->nullable()->after('token');
            
            // Agregar descripción del tipo de modificación
            $table->text('descripcion_solicitud')->nullable()->after('datos_solicitados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_modificacion_pastores', function (Blueprint $table) {
            $table->string('telefono_nuevo')->nullable()->after('token');
            $table->dropColumn(['datos_solicitados', 'descripcion_solicitud']);
        });
    }
};
