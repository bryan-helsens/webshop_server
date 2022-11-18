<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'street1' => $this->faker->text(50),
            'street2' => $this->faker->text(50),
            'city' => $this->faker->text(20),
            'country' => $this->faker->text(20),
            'state' => $this->faker->text(20),
            'zipcode' => $this->faker->numberBetween(4, 8),
        ];
    }
}
