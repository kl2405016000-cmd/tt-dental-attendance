<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminEmail = config('mail.mailers.admin_smtp.username') ?: 'ttdentalattendancettdental@gmail.com';
        $admin = User::where('role', 'admin')->first() ?? User::firstOrNew(['email' => $adminEmail]);

        if (!$admin->exists) {
            $admin->name = 'Administrator';
            $admin->email = $adminEmail;
            $admin->password = \Illuminate\Support\Facades\Hash::make('admin123');
        } elseif ($admin->email === 'admin@ttdental.com') {
            $admin->email = $adminEmail;
        }

        $admin->role = 'admin';
        $admin->save();
    }
}
