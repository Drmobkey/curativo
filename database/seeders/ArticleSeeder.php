<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tags;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Article::factory(10)->create()->each(function ($article) {
            // Attach 1-3 random categories to each article
            $categories = Category::inRandomOrder()->limit(rand(1, 3))->get();
            $article->categories()->attach($categories->pluck('id'));

            // Attach 2-5 random tags to each article
            $tags = Tags::inRandomOrder()->limit(rand(2, 5))->get();
            $article->tags()->attach($tags->pluck('id'));
        });
    }
}
