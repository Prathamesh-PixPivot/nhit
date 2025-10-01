<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Creating Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Admin', 
            'username' => 'superadmin', 
            'email' => 'superadmin@getnada.com',
            'password' => '123456'
        ]);
        $superAdmin->assignRole('Super Admin');

        // Creating Admin User
        $admin = User::create([
            'name' => 'Admin', 
            'username' => 'admin', 
            'email' => 'admin@agetnada.com',
            'password' => '123456'
        ]);
        $admin->assignRole('Admin');

        // Creating Product Manager User
        $manager = User::create([
            'name' => 'Kushal Vats', 
            'username' => 'kvats', 
            'email' => 'kvats69@gmail.com',
            'password' => '123456'
        ]);
        $manager->assignRole('Manager');

        // Creating Application User
        $user = User::create([
            'name' => 'Kushal Pal Sharma', 
            'username' => 'kushal', 
            'email' => 'ksharma.sharma27@gmail.com',
            'password' => '123456'
        ]);
        $user->assignRole('User');
    }
}