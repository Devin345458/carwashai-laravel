<?php

namespace Database\Seeders;

use App\Models\ItemType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user = User::first();
        ItemType::upsert([
            ['id' => 1, 'name' => 'Parts', 'company_id' => 1, 'created_by_id' => $user->id, 'updated_by_id' => $user->id],
            ['id' => 2, 'name' => 'Tools', 'company_id' => 1, 'created_by_id' => $user->id, 'updated_by_id' => $user->id],
            ['id' => 3, 'name' => 'Consumables', 'company_id' => 1, 'created_by_id' => $user->id, 'updated_by_id' => $user->id],
            ['id' => 4, 'name' => 'Chemicals', 'company_id' => 1, 'created_by_id' => $user->id, 'updated_by_id' => $user->id],
            ['id' => 5, 'name' => 'Vending', 'company_id' => 1, 'created_by_id' => $user->id, 'updated_by_id' => $user->id],
        ], ['id']);
    }
}
