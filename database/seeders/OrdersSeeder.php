<?php

namespace Database\Seeders;

use App\Models\Orders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       /*  $client = User::query()->where('role', 'client')->first();

        if(!$client) {
            throw new \Exception('Client user not found');
        }

        Orders::query()->create([
            'client_id' => $client->id,
            'status' => 'pending'
        ]); */
    }
}
