<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'event_date_time' => fake()->dateTimeBetween('now', '+1 year'),
            'video' => fake()->optional()->url(),
            'banner_image' => fake()->optional()->url(),
            'other_information' => fake()->optional()->paragraph(),
        ];
    }
}
