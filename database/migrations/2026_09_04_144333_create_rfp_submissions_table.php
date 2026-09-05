<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rfp_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('track');
            $table->string('organisation');
            $table->string('contact_name');
            $table->string('role')->nullable();
            $table->string('email');
            $table->string('telephone')->nullable();
            $table->string('country')->nullable();
            $table->string('budget_band')->nullable();
            $table->string('timeline')->nullable();
            $table->text('scope_summary');
            $table->json('service_interests')->nullable();
            $table->boolean('nda_required')->default(false);
            $table->string('source_page')->nullable();
            $table->ipAddress('submitted_ip')->nullable();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();

            $table->index(['track', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfp_submissions');
    }
};
