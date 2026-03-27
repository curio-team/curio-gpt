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
        Schema::create('agent_observations', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->string('agent_config_id', 36)->index();
            $table->string('user_id', 100)->nullable()->index();
            $table->string('conversation_id', 36)->nullable()->index();
            $table->string('category', 100)->nullable();
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_observations');
    }
};
