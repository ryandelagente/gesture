<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_sla_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('priority', 20); // low|medium|high|critical
            $table->unsignedSmallInteger('respond_hours')->default(48);
            $table->unsignedSmallInteger('resolve_hours')->default(168);
            $table->timestamps();
            $table->unique(['project_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_sla_policies');
    }
};
