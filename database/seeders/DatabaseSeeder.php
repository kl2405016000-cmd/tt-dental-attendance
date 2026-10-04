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
        $admin = User::firstOrNew(['email' => 'admin@ttdental.com']);

        if (!$admin->exists) {
            $admin->name = 'Administrator';
            $admin->password = \Illuminate\Support\Facades\Hash::make('admin123');
        }

        $admin->role = 'admin';
        $admin->save();
    }
}
