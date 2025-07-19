<?php

namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class BuildingSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Coordinates bounding box for Punjab, Pakistan
        $minLat = 29.30;  // Southernmost point of Punjab
        $maxLat = 33.00;  // Northernmost point of Punjab
        $minLng = 70.30;  // Westernmost point of Punjab
        $maxLng = 75.30;  // Easternmost point of Punjab

        // Common cities in Punjab, Pakistan
        $punjabCities = [
            'Lahore',
            'Faisalabad',
            'Rawalpindi',
            'Multan',
            'Gujranwala',
            'Sialkot',
            'Bahawalpur',
            'Sargodha',
            'Jhang',
            'Sheikhupura',
            'Rahim Yar Khan',
            'Gujrat',
            'Kasur',
            'Okara',
            'Sahiwal',
            'Wah Cantonment',
            'Chiniot',
            'Kamoke',
            'Hafizabad',
            'Mandi Bahauddin'
        ];

        // Create 80 new buildings
        for ($i = 1; $i <= 80; $i++) {
            Building::create([
                'name' => 'New Building ' . $i,
                'building_no' => 'NB-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'type' => 'new',
                'location' => $punjabCities[array_rand($punjabCities)],
                'latitude' => $faker->latitude($minLat, $maxLat),
                'longitude' => $faker->longitude($minLng, $maxLng),
                'status' => 'planned',
                'is_active' => true,
                'created_by' => 1, // Assuming user ID 1 is admin
                'updated_by' => 1,
            ]);
        }

        // Create 23 old buildings
        for ($i = 1; $i <= 23; $i++) {
            Building::create([
                'name' => 'Old Building ' . $i,
                'building_no' => 'OB-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'type' => 'old',
                'location' => $punjabCities[array_rand($punjabCities)],
                'latitude' => $faker->latitude($minLat, $maxLat),
                'longitude' => $faker->longitude($minLng, $maxLng),
                'status' => 'planned',
                'is_active' => true,
                'created_by' => 1, // Assuming user ID 1 is admin
                'updated_by' => 1,
            ]);
        }
    }
}
