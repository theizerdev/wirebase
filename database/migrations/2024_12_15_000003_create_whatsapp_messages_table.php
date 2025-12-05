<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id')->nullable()->unique();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->string('recipient_phone');
            $table->string('recipient_name')->nullable();
            $table->text('message_content');
            $table->json('variables')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->text('error_message')->nullable();
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->decimal('cost', 8, 4)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('template_id')->references('id')->on('whatsapp_templates')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->index('message_id');
            $table->index('recipient_phone');
            $table->index('status');
            $table->index('direction');
            $table->index('sent_at');
            $table->index(['status', 'created_at']);
            $table->index(['recipient_phone', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};