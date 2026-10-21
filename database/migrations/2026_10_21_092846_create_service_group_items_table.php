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
        Schema::create('service_group_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_group_id')->constrained()->cascadeOnDelete();
            $table->json('text');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['service_group_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_group_items');
    }
};
