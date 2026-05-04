<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            BusinessSeeder::class,
            PermissionTableSeeder::class,
            MasterDataSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
