<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('live_url', 512)->nullable()->after('description');
            $table->string('staging_url', 512)->nullable()->after('live_url');
            $table->string('admin_username', 191)->nullable()->after('staging_url');
            $table->text('admin_password_enc')->nullable()->after('admin_username');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['live_url', 'staging_url', 'admin_username', 'admin_password_enc']);
        });
    }
};
