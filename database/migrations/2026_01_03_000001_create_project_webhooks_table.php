<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 100);
            $table->string('target_url', 512);
            $table->string('platform', 20)->default('auto'); // auto|slack|teams|discord|generic
            $table->json('events')->nullable(); // ['bug.created','bug.assigned','bug.status_changed']
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('last_sent_at')->nullable();
            $table->unsignedInteger('fail_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_webhooks');
    }
};
