<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            if (! Schema::hasColumn('agent_conversations', 'revoked_at')) {
                $table->timestamp('revoked_at')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('agent_conversations', function (Blueprint $table) {
            if (Schema::hasColumn('agent_conversations', 'revoked_at')) {
                $table->dropColumn('revoked_at');
            }
        });
    }
};
