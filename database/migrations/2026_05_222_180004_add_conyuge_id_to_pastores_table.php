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
        Schema::table('pastores', function (Blueprint $table) {
            $table->unsignedBigInteger('conyuge_id')->nullable()->after('nombre_conyuge');
            $table->foreign('conyuge_id')->references('id')->on('pastores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pastores', function (Blueprint $table) {
            $table->dropForeign(['conyuge_id']);
            $table->dropColumn('conyuge_id');
        });
    }
};