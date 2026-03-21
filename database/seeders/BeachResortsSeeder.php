<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BeachResortsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the 4 main beach resorts.
     */
    public function run(): void
    {
        $beachResorts = [
            [
                'name' => 'RANIOLA BEACH RESORT',
                'description' => 'Family-friendly beach resort in Catmon with calm waters and complete amenities. Perfect for weekend getaways.',
                'latitude' => 10.6980,
                'longitude' => 124.0020,
                'rating' => 4,
                'address' => 'Catmon, Cebu',
                'fees' => 'Entrance: Free, Cottage rental required',
                'facilities' => 'Resort, Swimming Pool, Kiddie Pool, Restaurant, Videoke, Parking, Events Area',
                'cottage' => 'Small ₱600, Large ₱1,800, Pavilion ₱3,500',
            ],
            [
                'name' => 'HINAGDAN BEACH RESORT',
                'description' => 'Local beach resort in Catmon area with calm waters and relaxing atmosphere.',
                'latitude' => 10.7010,
                'longitude' => 124.0100,
                'rating' => 4,
                'address' => 'Catmon, Cebu',
                'fees' => 'Entrance: ₱40',
                'facilities' => 'Resort, Cottages, Swimming Area, Parking, Restrooms',
                'cottage' => 'Small ₱450, Large ₱1,300',
            ],
            [
                'name' => 'MAJESTIC BEACH RESORT',
                'description' => 'Beautiful beach resort in Catmon with pristine shoreline and family-friendly amenities.',
                'latitude' => 10.7080,
                'longitude' => 124.0050,
                'rating' => 5,
                'address' => 'Catmon, Cebu',
                'fees' => 'Entrance: ₱60',
                'facilities' => 'Resort, White Sand, Restaurant, Cottages, Parking, Wi-Fi',
                'cottage' => 'Small ₱600, Large ₱1,800',
            ],
            [
                'name' => 'TURTLE POINT BEACH RESORT',
                'description' => 'Scenic beach spot in Catmon known for occasional turtle sightings and snorkeling.',
                'latitude' => 10.6950,
                'longitude' => 124.0180,
                'rating' => 4,
                'address' => 'Catmon, Cebu',
                'fees' => 'Entrance: ₱50, Snorkeling: ₱100',
                'facilities' => 'Resort, Snorkel Area, Cottages, Restrooms, Parking, Snorkel Gear',
                'cottage' => 'Small ₱500, Large ₱1,500',
            ],
        ];

        foreach ($beachResorts as $resort) {
            Location::create($resort);
        }
    }
}
