<?php

namespace Database\Factories;

use App\Models\InjuryHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as FakerFactory;

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
            'user_id' => $user->id,
            'label' => $faker->word(), 
            'image' => $this->faker->imageUrl(),
            'location' => $faker->address(),  
            'notes' => $faker->text(200), 
            'detected_at' => $faker->dateTimeThisYear(),
            'created_by' => $user->id,
            'updated_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
