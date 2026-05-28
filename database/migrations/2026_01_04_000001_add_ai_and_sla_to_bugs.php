<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->json('ai_suggestions')->nullable()->after('perf_metrics');
            $table->timestamp('due_at')->nullable()->after('end_date');
            $table->timestamp('first_response_at')->nullable()->after('due_at');
            $table->timestamp('resolved_at')->nullable()->after('first_response_at');
        });
    }

    public function down(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->dropColumn(['ai_suggestions', 'due_at', 'first_response_at', 'resolved_at']);
        });
    }
};
