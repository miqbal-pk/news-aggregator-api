<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Article;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'source_id' =>fake()->words(3, true),
            'source_name' => fake()->words(3, true), 
            'author' => fake()->name(),
            'category'=> fake()->word(),
            'title' => fake()->sentence(),
            'description' =>fake()->paragraphs(2, true),
            'url'=> fake()->url(),
            'url_to_image' => fake()->imageUrl(), 
            'content' =>fake()->realText(700),
            'published_at' => now(),
        ];
    }
}
