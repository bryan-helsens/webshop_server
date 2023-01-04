<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->words($nb = 4, $asText = true),
            'short_description' => $this->faker->text(200),
            'description' => $this->faker->text(500),
            'price' => $this->faker->numberBetween(10, 200),
            'SKU' => 'FASH' . $this->faker->unique()->numberBetween(100, 500),
            'stock_status' => 'in_stock',
            'published' => $this->faker->boolean(),
            'quantity' => $this->faker->numberBetween(100, 200),
            'image' => $this->faker->unique()->numberBetween(1, 20) . '.jpeg',
            'category_id' => Category::inRandomOrder()->take(1)->first()->id,
        ];
    }
}
