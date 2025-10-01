<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-role',
            'create-role',
            'edit-role',
            'delete-role',

            'view-user',
            'create-user',
            'edit-user',
            'delete-user',
            
            'view-product',
            'create-product',
            'edit-product',
            'delete-product',
            
            'view-beneficiary',
            'create-beneficiary',
            'edit-beneficiary',
            'delete-beneficiary',
            
            'view-payment',
            'create-payment',
            'edit-payment',
            'delete-payment',
            
            'import-payment-excel',
         ];
 
          // Looping and Inserting Array's Permissions into Permission Table
         foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
          }
    }
}