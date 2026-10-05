<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Safely seed the three allowed geolocation locations into settings table.
     */
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            $defaultSettings = [
                // Location 1: Workplace
                'location_1_name' => 'Workplace',
                'location_1_address' => 'Shop Apartment, No. 20-1F (1), Jalan Orkid 4, Taman Orkid, 43200 Cheras, Selangor, Malaysia.',
                'location_1_lat' => '3.0783092',
                'location_1_lng' => '101.7664661',
                'location_1_radius' => '500',

                // Location 2: Home / Demo
                'location_2_name' => 'Home / Demo',
                'location_2_address' => 'The Sky Residensi, Taman Shamelin Perkasa, 56100 Kuala Lumpur, Malaysia.',
                'location_2_lat' => '3.1247527',
                'location_2_lng' => '101.7374223',
                'location_2_radius' => '500',

                // Location 3: College / Demo
                'location_3_name' => 'College / Demo',
                'location_3_address' => 'Universiti Poly-Tech Malaysia (UPTM), Jalan 6/91, Taman Shamelin Perkasa, 56100 Kuala Lumpur, Wilayah Persekutuan Kuala Lumpur.',
                'location_3_lat' => '3.1276441',
                'location_3_lng' => '101.7373364',
                'location_3_radius' => '500',

                // Keep legacy keys aligned with Location 1
                'workplace_lat' => '3.0783092',
                'workplace_lng' => '101.7664661',
                'workplace_radius' => '500',
            ];

            foreach ($defaultSettings as $key => $value) {
                if (!Setting::where('key', $key)->exists()) {
                    Setting::create(['key' => $key, 'value' => $value]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings')) {
            Setting::whereIn('key', [
                'location_1_name',
                'location_1_address',
                'location_1_lat',
                'location_1_lng',
                'location_1_radius',
                'location_2_name',
                'location_2_address',
                'location_2_lat',
                'location_2_lng',
                'location_2_radius',
                'location_3_name',
                'location_3_address',
                'location_3_lat',
                'location_3_lng',
                'location_3_radius',
            ])->delete();
        }
    }
};
