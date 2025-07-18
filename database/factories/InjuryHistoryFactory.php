<?php

namespace Database\Factories;

use App\Models\InjuryHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Str;

class InjuryHistoryFactory extends Factory
{
    public function definition(): array
    {
        // Set faker locale ke Indonesia
        $faker = FakerFactory::create('id_ID');

        $user = User::inRandomOrder()->first();

        if (!$user) {
            throw new \Exception('Tidak ada user di database. Silakan jalankan UserSeeder terlebih dahulu.');
        }

        return [
        'id' => Str::uuid(),
        'user_id' => $user->id,
        'label' => $faker->randomElement(['Luka Bakar', 'Luka Sayat', 'Luka Gores', 'Luka Lebam']),
        'image' => $faker->imageUrl(640, 480, 'medical', true),
        'location' => $faker->address(),
        'notes' => $faker->sentence(10),
        'recommendation' => $faker->text(),
        'detected_at' => $faker->dateTimeBetween('-6 months', 'now'),
        'scores' => $faker->randomFloat(2, 0, 1), // nilai antara 0.00 - 1.00

        'created_by' => $user->id,
        'updated_by' => $user->id,
        'created_at' => now(),
        'updated_at' => now(),
    ];
    }
}
