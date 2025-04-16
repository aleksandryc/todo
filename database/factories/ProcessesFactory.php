<?php

namespace Database\Factories;

use App\Models\Processes;
use App\Models\Tables;
use App\Models\Workshops;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Processes>
 */
class ProcessesFactory extends Factory
{
    protected $model = Processes::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'table_id' => Tables::factory(),
            'workshops_id' => Workshops::factory(),
            'status' => 'in_progress'
        ];
    }
}
