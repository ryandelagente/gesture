<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bug_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);
            $table->string('color', 16)->default('#6366f1');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['workspace_id', 'name']);
        });

        Schema::create('bug_tag_bug', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bug_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bug_tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['bug_id', 'bug_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bug_tag_bug');
        Schema::dropIfExists('bug_tags');
    }
};
