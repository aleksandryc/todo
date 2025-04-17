<?php

namespace Database\Seeders;

use App\Models\Orders;
use App\Models\Processes;
use App\Models\Tables;
use App\Models\User;
use App\Models\Workshops;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\Random;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create();

        // Create an admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.ca',
            'role' => 'admin',
        ]);

        // Create a worker
        User::factory()->create([
            'name' => 'Worker User',
            'email' => 'worker@example.com',
            'role' => 'worker',
        ]);

        // Create a client
        User::factory()->create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'role' => 'client',
        ]);

        // Create workshops
        Workshops::factory()->create([
            'name' => 'acceptance',
        ]);
        Workshops::factory()->create([
            'name' => 'painting',
        ]);
        Workshops::factory()->create([
            'name' => 'assembly',
        ]);
        Workshops::factory()->create([
            'name' => 'delivery',
        ]);

        // Create orders and processes
        Orders::factory()->count(50)->hasTables(2)->create();
        Tables::all()->each(function ($table) {
            Processes::factory()->create([
                'table_id' => $table->id,
                'workshops_id' => Workshops::query()->pluck('id')->random(),
            ]);
        });
    }
}
