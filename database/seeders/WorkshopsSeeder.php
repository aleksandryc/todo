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
      /*   // Find a worker
        $worker = User::query()->where('role', 'worker')->first();

        if(!$worker) {
            throw new \Exception('Worker user not found, create worker first');
        }

        // Create workshop for worker
        Workshops::query()->create([
            'name' => 'Painting workshop',
            'user_id'  => $worker->id,
        ]);
        Workshops::query()->create([
            'name' => 'Assembly workshop',
            'user_id'  => $worker->id,
        ]); */
    }
}
