<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_ticket_templates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->unsignedInteger('mailbox_id');
            $table->string('subject');
            $table->longText('body')->nullable();
            $table->string('rrule');
            $table->dateTime('starts_at');
            $table->string('timezone', 64)->default('UTC');
            $table->dateTime('next_run_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('max_runs')->nullable();
            $table->unsignedInteger('run_count')->default(0);
            $table->boolean('active')->default(true);
            $table->longText('payload_json')->nullable();
            $table->timestamps();
            $table->index(['active', 'next_run_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_ticket_templates');
    }
};
