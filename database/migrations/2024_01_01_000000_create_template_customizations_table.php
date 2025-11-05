<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_customizations', function (Blueprint $table) {
            $table->id();
            $table->string('primary_color')->default('#7367F0');
            $table->integer('skin')->default(0);
            $table->string('theme')->default('light');
            $table->boolean('semi_dark')->default(false);
            $table->string('content_layout')->default('compact');
            $table->string('header_type')->default('static');
            $table->boolean('menu_collapsed')->default(false);
            $table->string('navbar_type')->default('sticky');
            $table->string('text_direction')->default('ltr');
            $table->boolean('footer_fixed')->default(false);
            $table->boolean('dropdown_on_hover')->default(false);
            $table->string('layout_type')->default('vertical');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_customizations');
    }
};