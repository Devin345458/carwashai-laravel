<?php

namespace Database\Seeders;

use App\Models\TransactionAction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        TransactionAction::upsert([
            ['id' => 1, 'name' => 'Initial Stock', 'operation' => 2],
            ['id' => 2, 'name' => 'Received Stock', 'operation' => 0],
            ['id' => 3, 'name' => 'Stock Used', 'operation' => 1],
            ['id' => 4, 'name' => 'Used in repair', 'operation' => 1],
            ['id' => 5, 'name' => 'Used in maintenance', 'operation' => 1],
            ['id' => 6, 'name' => 'Inventory Conducted', 'operation' => 2],
        ], ['id']);
    }
}
