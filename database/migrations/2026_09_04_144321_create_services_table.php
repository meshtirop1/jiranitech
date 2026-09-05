<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->text('executive_summary');
            $table->json('outcomes');
            $table->json('capabilities');
            $table->json('stack');
            $table->text('architecture_note')->nullable();
            $table->string('sla_tier')->default('Gold');
            $table->json('compliance_tags');
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['pillar_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
