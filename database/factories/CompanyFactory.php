<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompanyFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name' => $name,
            'document' => fake()->numerify('##.###.###/####-##'),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'responsible_name' => fake()->name(),
            'slug' => Str::slug($name).'-'.fake()->numberBetween(1, 9999),
            'status' => 'active',
        ];
    }
}
