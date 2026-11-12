<?php

declare(strict_types=1);

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;
use Workbench\App\Values\TestEnum;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'status' => fake()->randomElement(TestEnum::cases()),
            'is_published' => fake()->boolean(),
            'views' => fake()->numberBetween(0, 1000),
            'meta' => ['key' => fake()->word()],
            'published_on' => fake()->date(),
            'published_at' => fake()->dateTime(),
        ];
    }
}
