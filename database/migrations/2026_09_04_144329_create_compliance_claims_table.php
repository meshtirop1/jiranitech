<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Publication gate G-02. `status` is required so a standard we follow but do not
     * hold renders as "Aligned - not certified" rather than as a certification.
     */
    public function up(): void
    {
        Schema::create('compliance_claims', function (Blueprint $table) {
            $table->id();
            $table->string('standard');
            $table->string('status');
            $table->string('evidence_url')->nullable();
            $table->date('reviewed_on');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique('standard');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compliance_claims');
    }
};
