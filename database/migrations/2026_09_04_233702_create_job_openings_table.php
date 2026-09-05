<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Publication gate G-10. A job listing is an invitation to apply, so a role that is
     * not genuinely open wastes a candidate's time. `is_published` gates each listing
     * and the careers page renders a designed empty state when none are live.
     */
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('level');
            $table->string('discipline')->nullable();
            $table->string('location');
            $table->string('arrangement');
            $table->text('summary');
            $table->json('responsibilities');
            $table->json('requirements');
            $table->boolean('is_published')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_openings');
    }
};
