<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Seed the default application settings.
     *
     * These 7 rows match the defaults defined in the approved
     * database_schema.md (Group G — System Configuration).
     */
    public function run(): void
    {
        $settings = [
            [
                'key'   => 'general.restaurant_name',
                'value' => 'My Restaurant',
                'group' => 'general',
                'label' => 'Restaurant Name',
                'type'  => 'string',
            ],
            [
                'key'   => 'general.restaurant_phone',
                'value' => '',
                'group' => 'general',
                'label' => 'Phone Number',
                'type'  => 'string',
            ],
            [
                'key'   => 'general.restaurant_address',
                'value' => '',
                'group' => 'general',
                'label' => 'Address',
                'type'  => 'string',
            ],
            [
                'key'   => 'billing.currency_symbol',
                'value' => '$',
                'group' => 'billing',
                'label' => 'Currency Symbol',
                'type'  => 'string',
            ],
            [
                'key'   => 'billing.tax_rate',
                'value' => '15.00',
                'group' => 'billing',
                'label' => 'Tax Rate (%)',
                'type'  => 'float',
            ],
            [
                'key'   => 'billing.service_charge_rate',
                'value' => '10.00',
                'group' => 'billing',
                'label' => 'Service Charge (%)',
                'type'  => 'float',
            ],
            [
                'key'   => 'billing.receipt_footer',
                'value' => 'Thank you for dining with us!',
                'group' => 'billing',
                'label' => 'Receipt Footer',
                'type'  => 'string',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
