<?php

namespace Database\Factories;

use App\Models\Orders;
use App\Models\Tables;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tables>
 */
class TablesFactory extends Factory
{
    protected $model = Tables::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city().' table',
            'color' => $this->faker->colorName(),
            'material' => $this->faker->randomElement(['wood', 'metal', 'plastic']),
            'status' => 'pending',
            'price' => $this->faker->randomFloat(2, 100, 1000),
            'orders_id' => Orders::factory(),
        ];
    }
}
