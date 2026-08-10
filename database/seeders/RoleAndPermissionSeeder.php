<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{

    public function run(): void
    {
        // Role (၃) မျိုးကို စတင်တည်ဆောက်ခြင်း
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Instructor']);
        Role::create(['name' => 'Customer']);
    }
}