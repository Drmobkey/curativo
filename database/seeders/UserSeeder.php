<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Role
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // 2. Buat Permission
        $permissions = [
            'add_user',
            'edit_user',
            'get_user',
            'delete_user'
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // 3. Beri semua permission ke superadmin
        $superadminRole->givePermissionTo($permissions);

        // 4. Buat 15 User dengan Factory
        $users = User::factory(15)->create();

        // 5. Assign role ke user
        $users->each(function ($user, $index) use ($superadminRole, $adminRole, $userRole) {
            if ($index === 0) {
                $user->assignRole($superadminRole); // user pertama jadi superadmin
            } elseif ($index <= 3) {
                $user->assignRole($adminRole); // user ke-2 sampai ke-4 jadi admin
            } else {
                $user->assignRole($userRole); // sisanya jadi user biasa
            }
        });
    }
}
