<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();

        if (!$admin) {
            $this->command->warn('No admin user found.');
            return;
        }

        $categories = ['Pertolongan Pertama', 'Kedaruratan Umum', 'Kesehatan Anak'];

        foreach ($categories as $name) {
            Category::create([
                'id' => Str::uuid(),
                'name' => $name,
                'slug' => Str::slug($name),
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }
    }
}
