<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws Throwable
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();
            $this->call(CarWashAiCompanySeeder::class);
            $this->call(ItemTypeSeeder::class);
            $this->call(TransactionActionSeeder::class);
            $this->call(EquipmentSeeder::class);
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
