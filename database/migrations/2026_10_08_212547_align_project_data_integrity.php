<?php

use App\Models\Rider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'club_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('club_id')->nullable();
            });
        }

        if (! Schema::hasColumn('riders', 'club_id')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->unsignedBigInteger('club_id')->nullable();
            });
        }

        $this->migrateClubNames('users');
        $this->migrateClubNames('riders');
        $this->migrateRideDates();
        $this->migrateRegistrationKeys();

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

        if (Schema::hasColumn('tracks', 'lng')) {
            Schema::table('tracks', function (Blueprint $table) {
                $table->decimal('lng', 8, 5)->change();
            });
        }

        if (! Schema::hasColumn('tracks', 'surface_type')) {
            Schema::table('tracks', function (Blueprint $table) {
                $table->string('surface_type')->nullable();
            });
        }

        foreach (['users', 'riders'] as $tableName) {
            if (Schema::hasColumn($tableName, 'club') && Schema::hasColumn($tableName, 'club_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('club');
                });
            }

            if (! Schema::hasIndex($tableName, "{$tableName}_club_id_foreign")) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreign('club_id')->references('id')->on('clubs')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['users', 'riders'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'club')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('club')->nullable();
                });
            }

            DB::table($tableName)
                ->join('clubs', 'clubs.id', '=', "{$tableName}.club_id")
                ->update(['club' => DB::raw('clubs.name')]);

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['club_id']);
                $table->dropColumn('club_id');
            });
        }

        if (Schema::hasColumn('tracks', 'surface_type')) {
            Schema::table('tracks', function (Blueprint $table) {
                $table->dropColumn('surface_type');
            });
        }

        if (Schema::hasColumn('tracks', 'lng')) {
            Schema::table('tracks', function (Blueprint $table) {
                $table->decimal('lng', 7, 5)->change();
            });
        }

        if (Schema::hasColumn('riders', 'registration_key')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->dropUnique(['registration_key']);
                $table->dropColumn('registration_key');
            });
        }
    }

    private function migrateClubNames(string $tableName): void
    {
        if (! Schema::hasColumn($tableName, 'club')) {
            return;
        }

        $names = DB::table($tableName)
            ->whereNotNull('club')
            ->where('club', '<>', '')
            ->where('club', '<>', 'Privāti')
            ->distinct()
            ->pluck('club');

        foreach ($names as $name) {
            DB::table('clubs')->insertOrIgnore([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $clubId = DB::table('clubs')->where('name', $name)->value('id');
            DB::table($tableName)->where('club', $name)->update(['club_id' => $clubId]);
        }
    }

    private function migrateRideDates(): void
    {
        if (! Schema::hasColumn('riders', 'ride_date')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->date('ride_date')->nullable();
            });
        }

        DB::table('riders')->whereNull('ride_date')->update(['ride_date' => today()->toDateString()]);
    }

    private function migrateRegistrationKeys(): void
    {
        if (! Schema::hasColumn('riders', 'registration_key')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->string('registration_key', 64)->nullable();
            });
        }

        $usedKeys = [];
        DB::table('riders')->orderBy('id')->chunkById(200, function ($riders) use (&$usedKeys) {
            foreach ($riders as $rider) {
                $attributes = (array) $rider;
                $key = Rider::registrationKeyFor($attributes);

                if (isset($usedKeys[$key])) {
                    $key = hash('sha256', $key.'|legacy:'.$rider->id);
                }

                $usedKeys[$key] = true;
                DB::table('riders')->where('id', $rider->id)->update(['registration_key' => $key]);
            }
        });

        Schema::table('riders', function (Blueprint $table) {
            $table->string('registration_key', 64)->nullable(false)->change();
        });

        if (! Schema::hasIndex('riders', 'riders_registration_key_unique')) {
            Schema::table('riders', function (Blueprint $table) {
                $table->unique('registration_key');
            });
        }
    }
};
