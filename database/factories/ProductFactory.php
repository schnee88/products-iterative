<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'quantity' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(10),
            'expiration_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['available', 'out_of_stock', 'discontinued']),
        ];
    }
}
