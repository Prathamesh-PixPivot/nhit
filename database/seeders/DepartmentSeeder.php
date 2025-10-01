<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Operations', 'description' => 'Human Resources'], //
            ['name' => 'HR & Admin', 'description' => 'Information Technology'],
            ['name' => 'IT', 'description' => 'Information Technology'],
            ['name' => 'EHS', 'description' => 'EHS Department'],
            ['name' => 'Finance & Accounts', 'description' => 'Marketing & Sales'],
            ['name' => 'Secretarial', 'description' => 'Secretarial & Logistics'],
            ['name' => 'Procurement', 'description' => 'Procurement & Logistics'],
            ['name' => 'I & A', 'description' => 'I & A & Logistics'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
