<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Business;
use App\Models\Category;
use App\Models\TaxRate;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::first();
        if (! $business) {
            return;
        }

        $user = $business->owner;

        $categories = [
            ['name' => 'Makanan', 'short_code' => 'FD'],
            ['name' => 'Minuman', 'short_code' => 'DR'],
            ['name' => 'Snack', 'short_code' => 'SN'],
            ['name' => 'Rokok', 'short_code' => 'RK'],
            ['name' => 'Perlengkapan', 'short_code' => 'SP'],
            ['name' => 'Lainnya', 'short_code' => 'OT'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'short_code' => $cat['short_code'],
                'business_id' => $business->id,
                'created_by' => $user->id,
            ]);
        }

        $brands = ['Indofood', 'Wings', 'Unilever', 'Sampoerna', 'Gudang Garam', 'Djarum', 'Mayora', 'Garuda Food', 'ABC', 'Kraft'];

        foreach ($brands as $name) {
            Brand::create([
                'name' => $name,
                'business_id' => $business->id,
                'created_by' => $user->id,
            ]);
        }

        $units = [
            ['actual_name' => 'Pcs', 'short_name' => 'Pcs', 'allow_decimal' => false],
            ['actual_name' => 'Pack', 'short_name' => 'Pack', 'allow_decimal' => true],
            ['actual_name' => 'Dus', 'short_name' => 'Dus', 'allow_decimal' => true],
            ['actual_name' => 'Kg', 'short_name' => 'Kg', 'allow_decimal' => true],
            ['actual_name' => 'Gram', 'short_name' => 'g', 'allow_decimal' => true],
            ['actual_name' => 'Liter', 'short_name' => 'L', 'allow_decimal' => true],
            ['actual_name' => 'Botol', 'short_name' => 'Btl', 'allow_decimal' => false],
        ];

        foreach ($units as $unit) {
            Unit::create(array_merge($unit, [
                'business_id' => $business->id,
                'created_by' => $user->id,
            ]));
        }

        TaxRate::create([
            'name' => 'PPN 11%',
            'amount' => 11,
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);

        TaxRate::create([
            'name' => 'Non PPN',
            'amount' => 0,
            'business_id' => $business->id,
            'created_by' => $user->id,
        ]);
    }
}
