<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagement_models', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('reference', 16);
            $table->string('title');
            $table->text('summary');
            $table->text('commercial_basis');
            $table->text('scope_boundary');
            $table->text('governance_cadence');
            $table->text('suited_to');
            $table->string('rfp_track')->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagement_models');
    }
};
