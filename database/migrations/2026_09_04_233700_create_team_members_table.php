<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Publication gate G-09. `name` and `photo_path` are nullable on purpose: a post
     * exists and is accountable whether or not the appointment has been announced, and
     * the page must be able to state the accountability without inventing a person.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('role_title');
            $table->string('discipline')->nullable();
            $table->text('accountability');
            $table->json('remit');
            $table->string('photo_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
