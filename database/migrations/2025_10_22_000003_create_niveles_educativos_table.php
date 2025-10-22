<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('niveles_educativos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('costo', 10, 2);
            $table->integer('cuotas');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('niveles_educativos');
    }
};
