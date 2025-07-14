<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

     protected $model = Article::class;

    public function definition(): array
    {
        $faker = FakerFactory::create('id_ID');
        $User = User::inRandomOrder()->first()??User::factory()->create();
        $title = $faker->sentence();
        return [
                'id' => Str::uuid(),
                'title' => $title,
                'slug' => Str::slug($title),
                'content' => $faker->paragraph(3, true),
                'image' => $faker->imageUrl(640, 480, 'cats', true),
                'status' => $faker->randomElement(['draft', 'published']),
                'created_by' => $User->id,
                'updated_by' => $User->id,
                'created_at' => now(),
                'updated_at' => now(),
            
        ];
    }
}
