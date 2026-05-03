<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\BusinessLocation;
use App\Models\InvoiceLayout;
use App\Models\InvoiceScheme;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@smartpos.com'],
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'password' => bcrypt('password'),
                'first_name' => 'Admin',
                'last_name' => 'System',
                'language' => 'id',
                'status' => 'active',
            ]
        );

        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $user->assignRole($role);

        $business = Business::create([
            'name' => 'SmartPOS',
            'owner_id' => $user->id,
            'time_zone' => 'Asia/Jakarta',
            'fy_start_month' => 1,
            'sell_price_tax' => 'excludes',
            'theme_color' => 'blue',
            'date_format' => 'd-m-Y',
            'time_format' => '24',
            'is_active' => true,
        ]);

        $user->update(['business_id' => $business->id]);

        $location = BusinessLocation::create([
            'business_id' => $business->id,
            'name' => 'Toko Utama',
            'country' => 'Indonesia',
            'city' => 'Jakarta',
        ]);

        $invoiceScheme = InvoiceScheme::create([
            'business_id' => $business->id,
            'name' => 'Default',
            'scheme_type' => 'blank',
            'prefix' => 'INV',
            'start_number' => 1,
            'invoice_count' => 0,
            'total_digits' => 6,
            'is_default' => true,
        ]);

        InvoiceLayout::create([
            'name' => 'Default',
            'business_id' => $business->id,
            'is_default' => true,
            'show_business_name' => true,
            'show_location_name' => true,
            'show_customer' => true,
            'design' => 'classic',
        ]);
    }
}
