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
        $startDate = fake()->dateTimeBetween('now', '+1 year');
        $endDate = fake()->dateTimeBetween($startDate, '+3 days');
        
        return [
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'from_date' => $startDate,
            'to_date' => $endDate,
            'video' => fake()->optional()->url(),
            'banner_image' => fake()->optional()->url(),
            'other_information' => fake()->optional()->paragraph(),
        ];
    }
}
