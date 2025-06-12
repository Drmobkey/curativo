<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Category::class;
    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');
        $name = $faker->unique()->word();
        $user = User::inRandomOrder()->first()??User::factory()->create();
        return [
            //
            'name'=> $name,
            'slug'=> Str::slug($name),
            'created_by'=> $user->id,
            'updated_by'=> $user->id,
            'created_at'=> now(),
            'updated_at'=> now(),
            
        ];
    }
}
