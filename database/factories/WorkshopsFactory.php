<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Workshops;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workshops>
 */
class WorkshopsFactory extends Factory
{
    protected $model = Workshops::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Retrieve or create a single worker
        $worker = User::firstOrCreate(
            ['role' => 'worker'],
            ['name' => 'Default Worker', 'email' => 'worker@example.com', 'password' => bcrypt('password')]
        );

        return [
            'name' => $this->faker->randomElement(['acceptance', 'painting', 'assembly', 'delivery']),
            'max_tables' => 3,
            'user_id' => $worker->id,
        ];
    }
}
