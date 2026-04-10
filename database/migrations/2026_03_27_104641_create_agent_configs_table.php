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
        Schema::create('agent_configs', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('created_by', 36)->nullable()->index();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->string('name');
            $table->string('description', 300)->nullable();
            $table->string('image_path')->nullable();
            $table->text('instructions');
            $table->json('allowed_groups')->nullable();
            $table->json('allowed_models')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('history_is_disabled')->default(false);
            $table->time('available_from')->nullable();
            $table->time('available_until')->nullable();
            $table->boolean('monitoring_is_enabled')->default(false);
            $table->text('monitoring_instructions')->nullable();
            $table->string('monitoring_model', 100)->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_configs');
    }
};
