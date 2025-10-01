<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            ['name' => 'Manager', 'description' => 'Manages the team'], //
            ['name' => 'Senior Developer', 'description' => 'Experienced Developer'],
            ['name' => 'Junior Developer', 'description' => 'Entry-level Developer'],
            ['name' => 'HR Executive', 'description' => 'Handles HR tasks'],
            ['name' => 'Finance Analyst', 'description' => 'Manages financial reports'],
        ];

        foreach ($designations as $designation) {
            Designation::create($designation);
        }
    }
}
