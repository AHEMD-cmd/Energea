<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->json('footer_text');
            $table->string('general_email');
            $table->json('quote_recipients')->nullable();
            $table->json('departments')->nullable();
            $table->json('offices')->nullable();
            $table->json('social_links')->nullable();
            $table->string('og_image')->nullable();
            $table->string('ga4_measurement_id')->nullable();
            $table->string('search_console_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
