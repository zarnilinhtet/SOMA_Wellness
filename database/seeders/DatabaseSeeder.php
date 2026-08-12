<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);
        // Admin User တစ်ယောက်တည်းကို သတ်မှတ်ချက်အတိုင်း Create လုပ်ခြင်း
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'phone' => '00000',
            'age' => '1990-01-01',
            'password' => Hash::make('admin'), // 'admin' ကို bcrypt နဲ့ encrypt လုပ်ပေးပါမယ်

        ]);
        $admin->assignRole('Admin');

        $this->call([
            PermissionSeeder::class,
        ]);


        // လိုအပ်ရင် တခြား test users ၉ ယောက် ထပ်တိုးချင်ရင် ဒီ line ကို uncomment လုပ်နိုင်ပါတယ်
        // User::factory(9)->create();
    }
}
