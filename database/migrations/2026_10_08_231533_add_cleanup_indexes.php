<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasIndex('riders', 'riders_ride_date_index')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->index('ride_date');
            });
        }

        if (! Schema::hasIndex('track_comments', 'track_comments_created_at_index')) {
            Schema::table('track_comments', function (Blueprint $table) {
                $table->index('created_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('riders', 'riders_ride_date_index')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->dropIndex('riders_ride_date_index');
            });
        }

        if (Schema::hasIndex('track_comments', 'track_comments_created_at_index')) {
            Schema::table('track_comments', function (Blueprint $table) {
                $table->dropIndex('track_comments_created_at_index');
            });
        }
    }
};
