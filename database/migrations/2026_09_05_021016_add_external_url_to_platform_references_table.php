<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A group platform has its own public address. It is a sibling of this division,
     * not the parent, so the link belongs on the platform record rather than being
     * borrowed from the holding company's URL.
     */
    public function up(): void
    {
        Schema::table('platform_references', function (Blueprint $table) {
            $table->string('external_url')->nullable()->after('operating_since');
            $table->string('external_label')->nullable()->after('external_url');
        });
    }

    public function down(): void
    {
        Schema::table('platform_references', function (Blueprint $table) {
            $table->dropColumn(['external_url', 'external_label']);
        });
    }
};
