<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bug_widget_keys', function (Blueprint $table) {
            $table->string('brand_color', 16)->nullable()->after('allowed_origins');
            $table->string('brand_logo_url', 512)->nullable()->after('brand_color');
            $table->string('button_label', 60)->nullable()->after('brand_logo_url');
            $table->string('welcome_text', 280)->nullable()->after('button_label');
        });
    }

    public function down(): void
    {
        Schema::table('bug_widget_keys', function (Blueprint $table) {
            $table->dropColumn(['brand_color', 'brand_logo_url', 'button_label', 'welcome_text']);
        });
    }
};
