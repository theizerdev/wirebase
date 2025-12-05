<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->text('content');
            $table->json('variables')->nullable();
            $table->enum('category', ['notification', 'reminder', 'marketing', 'transactional', 'other'])->default('other');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index('category');
            $table->index('is_active');
            $table->index('usage_count');
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_templates');
    }
};