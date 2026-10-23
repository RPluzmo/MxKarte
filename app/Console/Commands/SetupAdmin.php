<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class SetupAdmin extends Command
{
    protected $signature = 'mxkarte:admin:setup';

    protected $description = 'Droši izveido vai atjauno administratora kontu';

    public function handle(): int
    {
        $email = mb_strtolower(trim($this->ask('Administratora e-pasts')));
        $existing = User::where('email', $email)->first();

        $name = trim($this->ask('Vārds', $existing?->name ?? 'Administrators'));
        $surname = trim($this->ask('Uzvārds', $existing?->surname ?? 'MxKarte'));
        $password = $this->secret('Jauna administratora parole');
        $confirmation = $this->secret('Atkārto paroli');

        $validator = Validator::make([
            'email' => $email,
            'name' => $name,
            'surname' => $surname,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'email' => ['required', 'email', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        $admin = $existing ?? new User;
        $admin->forceFill([
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ])->save();

        DB::table('sessions')->where('user_id', $admin->id)->delete();

        $this->info('Administratora konts ir saglabāts. Piesakies ar norādīto e-pastu un jauno paroli.');

        return self::SUCCESS;
    }

}
