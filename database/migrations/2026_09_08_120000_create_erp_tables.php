<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Authority across the system. Project-level standing is separate,
            // in project_members, because leadership is per engagement.
            $table->string('erp_role')->nullable()->after('is_admin');
            $table->string('job_title')->nullable()->after('erp_role');
            $table->boolean('is_active')->default(true)->after('job_title');
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();            // JTS-P-001, quotable in correspondence
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('client')->nullable();
            $table->string('status')->default('discovery');
            $table->text('summary')->nullable();

            // Where the code actually lives. Stored rather than integrated: an
            // API token would have to be held somewhere, and a link that a lead
            // can follow answers the question this system is asked.
            $table->string('repository_url')->nullable();
            $table->string('default_branch')->default('main');

            $table->date('started_on')->nullable();
            $table->date('target_date')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('engineer');
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('reference')->unique();       // JTS-P-001-014
            $table->string('title');
            $table->text('description')->nullable();

            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default('backlog');
            $table->unsignedSmallInteger('weight')->default(1);   // relative size, drives progress
            $table->date('due_on')->nullable();

            // The engineer's own record of what they did. Required before the
            // task may be submitted for review — see TaskWorkflow.
            $table->text('documentation')->nullable();

            $table->string('branch')->nullable();
            $table->string('pull_request_url')->nullable();
            $table->string('merge_reference')->nullable();        // commit or MR id, recorded at merge

            $table->text('blocked_reason')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index('assignee_id');
        });

        Schema::create('task_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->string('decision');                  // approved | changes_requested
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'created_at']);
        });

        Schema::create('task_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        /*
         * Append-only. Nothing in the application updates or deletes a row here,
         * because the value of this table is that it cannot be tidied: it is the
         * answer to "who accepted this, and when" months after the fact.
         */
        Schema::create('activity_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->string('action');
            $table->string('from_state')->nullable();
            $table->string('to_state')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['subject_type', 'subject_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_entries');
        Schema::dropIfExists('task_updates');
        Schema::dropIfExists('task_reviews');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['erp_role', 'job_title', 'is_active']);
        });
    }
};
