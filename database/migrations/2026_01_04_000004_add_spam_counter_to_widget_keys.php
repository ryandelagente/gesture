<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bug_widget_keys', function (Blueprint $table) {
            $table->unsignedInteger('spam_blocked_count')->default(0)->after('last_used_at');
            $table->timestamp('last_spam_at')->nullable()->after('spam_blocked_count');
        });
    }

    public function down(): void
    {
        Schema::table('bug_widget_keys', function (Blueprint $table) {
            $table->dropColumn(['spam_blocked_count', 'last_spam_at']);
        });
    }
};
