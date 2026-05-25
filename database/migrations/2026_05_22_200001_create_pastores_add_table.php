<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('pastores', function (Blueprint $table) {
            $table->unsignedInteger('empresa_id')->default(1)->after('cargo_nacional');
            $table->unsignedInteger('sucursal_id')->default(1)->after('cargo_nacional');
      
        });
    }

    public function down(): void
    {
        Schema::table('pastores', function (Blueprint $table) {
            $table->dropColumn(['empresa_id']);
        });
    }
};
