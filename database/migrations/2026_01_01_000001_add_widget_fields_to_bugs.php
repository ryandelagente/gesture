<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->string('page_url', 2048)->nullable()->after('environment');
            $table->string('element_selector', 1024)->nullable()->after('page_url');
            $table->unsignedSmallInteger('pin_x')->nullable()->after('element_selector');
            $table->unsignedSmallInteger('pin_y')->nullable()->after('pin_x');
            $table->unsignedSmallInteger('viewport_w')->nullable()->after('pin_y');
            $table->unsignedSmallInteger('viewport_h')->nullable()->after('viewport_w');
            $table->string('user_agent', 1024)->nullable()->after('viewport_h');
            $table->string('browser', 100)->nullable()->after('user_agent');
            $table->string('os', 100)->nullable()->after('browser');
            $table->string('screenshot_path')->nullable()->after('os');
            $table->string('guest_name', 100)->nullable()->after('screenshot_path');
            $table->string('guest_email', 150)->nullable()->after('guest_name');
            $table->string('source', 30)->default('internal')->after('guest_email');
        });

        Schema::table('bugs', function (Blueprint $table) {
            $table->unsignedBigInteger('reported_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->dropColumn([
                'page_url', 'element_selector', 'pin_x', 'pin_y',
                'viewport_w', 'viewport_h', 'user_agent', 'browser', 'os',
                'screenshot_path', 'guest_name', 'guest_email', 'source',
            ]);
        });
    }
};
