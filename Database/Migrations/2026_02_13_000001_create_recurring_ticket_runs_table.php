<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_ticket_runs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('template_id');
            $table->dateTime('scheduled_for');
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->string('result', 32)->default('pending');
            $table->text('message')->nullable();
            $table->timestamps();
            $table->unique(['template_id', 'scheduled_for']);
            $table->index(['result', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_ticket_runs');
    }
};
