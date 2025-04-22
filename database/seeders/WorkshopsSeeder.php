<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Workshops;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Queue\Worker;

class WorkshopsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Retrieve or create a single worker
        $worker = User::firstOrCreate(
            ['role' => 'worker'],
            ['name' => 'Worker Name', 'email' => 'worker@example.com', 'password' => bcrypt('password')]
        );

        // Create workshops and associate them with the worker
        $workshops = ['acceptance', 'painting', 'assembly', 'delivery'];

        foreach ($workshops as $workshopName) {
            Workshops::create([
                'name' => $workshopName,
                'max_tables' => 3,
                'user_id' => $worker->id,
            ]);
        }
    }
}
