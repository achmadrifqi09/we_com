<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRoleId = Role::where('name', 'Owner')->first()->id;
        User::create([
            'name' => 'Achmad Rifqi Rosadi',
            'username' => 'achmadrifqi09',
            'email' => 'achmadrifqi09@gmail.com',
            'phone' => '081231838322',
            'password' => 'rahasia',
            'role_id' => $ownerRoleId
        ]);
    }
}
