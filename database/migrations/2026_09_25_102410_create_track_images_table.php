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
        Schema::create('track_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['cover', 'gallery']);
            $table->string('path');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['track_id', 'type', 'sort_order']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_images');
    }
};
