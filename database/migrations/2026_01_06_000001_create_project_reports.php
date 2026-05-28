<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Project-level integration handles (manually pasted for now;
        // populated by GA4/GSC OAuth or service account in later phases)
        Schema::table('projects', function (Blueprint $table) {
            $table->string('ga4_property_id', 50)->nullable()->after('admin_password_enc');
            $table->string('gsc_site_url', 512)->nullable()->after('ga4_property_id');
            $table->string('gbp_location_id', 100)->nullable()->after('gsc_site_url');
            $table->string('business_phone', 50)->nullable()->after('gbp_location_id');
        });

        // Monthly KPI snapshots — one row per (project, period, metric)
        Schema::create('project_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');     // first day of month
            $table->date('period_end');       // last day of month
            $table->string('metric_key', 60); // sessions|users|page_views|leads|gmb_clicks|gmb_calls
            $table->decimal('metric_value', 14, 2)->nullable();
            $table->string('source', 30)->default('manual'); // manual|ga4|gsc|gbp
            $table->timestamps();
            $table->unique(['project_id', 'period_start', 'metric_key'], 'pm_unique');
            $table->index(['project_id', 'period_start']);
        });

        // SEO keyword rankings tracked per project
        Schema::create('project_keyword_rankings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('period_start');
            $table->string('keyword', 255);
            $table->unsignedSmallInteger('position')->nullable();
            $table->unsignedSmallInteger('previous_position')->nullable();
            $table->string('bucket', 30)->nullable(); // top3|progressing|long_tail
            $table->timestamps();
            $table->index(['project_id', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_keyword_rankings');
        Schema::dropIfExists('project_metrics');
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['ga4_property_id', 'gsc_site_url', 'gbp_location_id', 'business_phone']);
        });
    }
};
