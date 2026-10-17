<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('riders', 'registration_key')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->string('registration_key', 64)->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('riders', 'registration_key')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->string('registration_key', 64)->nullable()->change();
            });
        }
    }
};
