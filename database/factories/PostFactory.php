<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = $this->faker->sentence;

        return [
            'title'   => $title,
            'content' => $this->faker->paragraphs(asText: true, nb: 5),
            'slug'    => Str::slug($title) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
        ];
    }
}
