<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'enable_agency_mode')) {
                $table->string('enable_agency_mode', 3)->default('off')->after('enable_chatgpt');
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (Schema::hasColumn('plans', 'enable_agency_mode')) {
                $table->dropColumn('enable_agency_mode');
            }
        });
    }
};
