<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->nullable()->constrained()->nullOnDelete();
            $table->string('format');
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('abstract_line', 120);
            $table->longText('body')->nullable();
            $table->unsignedSmallInteger('read_minutes')->default(5);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['published_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('insights');
    }
};
