<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Tags;
use App\Models\User;

class TagsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::whereHas('role', fn($q) => $q->where('name', 'admin'))->first();

        if (!$admin) {
            $this->command->warn('No admin user found.');
            return;
        }

        $tags = ['Pingsan', 'Terkilir', 'Tersedak', 'Mimisan', 'Gigitan Ular', 'Kejang', 'Serangan Jantung', 'Demam'];

        foreach ($tags as $name) {
            Tags::create([
                'id' => Str::uuid(),
                'name' => $name,
                'slug' => Str::slug($name),
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        }
    }
}

