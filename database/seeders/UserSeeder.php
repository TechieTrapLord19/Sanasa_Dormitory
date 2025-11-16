<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DELETE THE TRUNCATE AND ALTER TABLE LINES.
        // The 'migrate:fresh' command already cleared the table.

        // 1. The Owner
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Owner',
            'email' => 'owner@app.com',
            'birth_date' => '1980-01-15',
            'address' => '123 Admin Street, Metro Manila',
            'role' => 'owner',
            'password' => Hash::make('password')
        ]);

        // 2. Caretaker 1
        User::create([
            'first_name' => 'Caretaker',
            'last_name' => 'One',
            'email' => 'caretaker1@app.com',
            'birth_date' => '1990-05-20',
            'address' => '456 Caretaker Avenue, Quezon City',
            'role' => 'caretaker',
            'password' => Hash::make('password')
        ]);

        // ... (rest of your users) ...

        User::create([
            'first_name' => 'Dev',
            'last_name' => 'Account',
            'email' => 'dev@app.com',
            'birth_date' => '1995-03-10',
            'address' => '789 Developer Road, Makati City',
            'role' => 'owner',
            'password' => Hash::make('password')
        ]);
    }
}
