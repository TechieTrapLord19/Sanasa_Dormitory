<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'middle_name' => fake()->optional()->lastName(), // optional() makes it sometimes NULL
            'last_name' => fake()->lastName(),
            'address' => fake()->address(),
            'birth_date' => fake()->date('Y-m-d', '2005-01-01'), // Max birth year of 2005
            'id_document' => 'id_docs/' . fake()->uuid() . '.jpg', // Simulates a file path
            'contact_num' => fake()->phoneNumber(),
            'emer_contact_num' => fake()->phoneNumber(),
            'status' => fake()->randomElement(['Active', 'Inactive']),
        ];
    }
}
