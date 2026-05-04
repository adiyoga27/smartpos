<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\Category;
use App\Models\Product;
use App\Models\TaxRate;
use App\Models\Unit;
use App\Models\Variation;
use App\Models\VariationLocationDetail;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $business = Business::first();
        if (! $business) {
            $this->command?->error('No business found. Run BusinessSeeder first.');

            return;
        }

        $user = $business->owner;

        $locationNames = [
            'CAKRA LOJI',
            'GUDANG 1',
            'GUNUNG SARI',
            'AGUS BARBER',
            'Kasful BARBER',
            'Ovi BARBER',
            'PAK IDE BARBER',
            'HOLID BARBER',
        ];

        $locations = [];
        foreach ($locationNames as $name) {
            $locations[$name] = BusinessLocation::firstOrCreate(
                ['business_id' => $business->id, 'name' => $name],
                ['country' => 'Indonesia']
            );
        }

        $defaultLocation = $locations['CAKRA LOJI'] ?? BusinessLocation::where('business_id', $business->id)->first();

        $taxRate = TaxRate::where('business_id', $business->id)->where('name', 'Non PPN')->first()
            ?? TaxRate::where('business_id', $business->id)->first();

        $unit = Unit::firstOrCreate(
            ['business_id' => $business->id, 'actual_name' => 'Pcs', 'short_name' => 'Pcs'],
            ['allow_decimal' => false, 'created_by' => $user->id]
        );

        $categoryName = 'Alat Musik';
        $categoryAlternative = Category::firstOrCreate(
            ['business_id' => $business->id, 'name' => $categoryName],
            ['short_code' => 'AM', 'created_by' => $user->id]
        );

        $categoryLainnya = Category::firstOrCreate(
            ['business_id' => $business->id, 'name' => 'Lainnya'],
            ['short_code' => 'LN', 'created_by' => $user->id]
        );

        $productsData = $this->getProductData();

        $existingSkus = Product::where('business_id', $business->id)->pluck('sku')->toArray();
        $count = 0;
        $updated = 0;

        foreach ($productsData as $data) {
            $sku = $data['sku'] ?: null;

            $product = Product::firstOrNew(
                ['business_id' => $business->id, 'sku' => $sku],
                [
                    'name' => $data['name'],
                    'type' => 'single',
                    'unit_id' => $unit->id,
                    'category_id' => $data['stock'] > 0 || str_contains(strtolower($data['name']), 'gitar') || str_contains(strtolower($data['name']), 'ampli') || str_contains(strtolower($data['name']), 'bass') ? $categoryAlternative->id : $categoryLainnya->id,
                    'tax_id' => $taxRate?->id,
                    'tax_type' => 'exclusive',
                    'enable_stock' => true,
                    'alert_quantity' => 0,
                    'created_by' => $user->id,
                ]
            );

            if ($product->exists && $product->wasChanged() === false) {
                $updated++;
                $product->update(['type' => 'single', 'enable_stock' => true]);
            }

            if (! $product->exists) {
                $product->save();
            }

            $variation = Variation::firstOrNew(
                ['product_id' => $product->id, 'name' => 'DUMMY'],
                [
                    'sub_sku' => $sku,
                    'default_purchase_price' => $data['purchase_price'],
                    'dpp_inc_tax' => $data['purchase_price'],
                    'profit_percent' => 0,
                    'default_sell_price' => $data['sell_price'],
                    'sell_price_inc_tax' => $data['sell_price'],
                ]
            );

            if (! $variation->exists) {
                $variation->save();
            } else {
                $variation->update([
                    'default_purchase_price' => $data['purchase_price'],
                    'dpp_inc_tax' => $data['purchase_price'],
                    'default_sell_price' => $data['sell_price'],
                    'sell_price_inc_tax' => $data['sell_price'],
                ]);
            }

            $productLocations = array_filter(array_map('trim', explode(',', $data['locations'] ?? '')));
            if (empty($productLocations)) {
                $productLocations = [$defaultLocation->name];
            }

            if ($data['stock'] > 0) {
                $stockPerLocation = intdiv($data['stock'], count($productLocations));
                $remainder = $data['stock'] % count($productLocations);

                foreach ($productLocations as $i => $locName) {
                    $loc = $locations[$locName] ?? $defaultLocation;
                    $qty = $stockPerLocation + ($i === 0 ? $remainder : 0);

                    if ($qty > 0) {
                        VariationLocationDetail::firstOrCreate(
                            [
                                'variation_id' => $variation->id,
                                'location_id' => $loc->id,
                            ],
                            [
                                'product_id' => $product->id,
                                'qty_available' => $qty,
                            ]
                        );
                    }
                }

                foreach (array_keys($locations) as $locName) {
                    if (! in_array($locName, $productLocations)) {
                        $loc = $locations[$locName];
                        VariationLocationDetail::firstOrCreate(
                            [
                                'variation_id' => $variation->id,
                                'location_id' => $loc->id,
                            ],
                            [
                                'product_id' => $product->id,
                                'qty_available' => 0,
                            ]
                        );
                    }
                }
            } else {
                foreach ($productLocations as $locName) {
                    $loc = $locations[$locName] ?? $defaultLocation;
                    VariationLocationDetail::firstOrCreate(
                        [
                            'variation_id' => $variation->id,
                            'location_id' => $loc->id,
                        ],
                        [
                            'product_id' => $product->id,
                            'qty_available' => 0,
                        ]
                    );
                }
            }

            $count++;
        }

        $this->command?->info("Seeded {$count} products ({$updated} already existed, ".($count - $updated).' created).');
    }

    private function getProductData(): array
    {
        return require __DIR__.'/../../storage/app/product_data.php';
    }
}
