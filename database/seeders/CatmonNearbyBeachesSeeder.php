<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CatmonNearbyBeachesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed nearby beaches for Catmon/Cebu area.
     */
    public function run(): void
    {
        $nearbyBeaches = [
            [
                'name' => 'GUIWANON BEACH RESORT',
                'description' => 'Relaxing beach resort with calm waters, perfect for family outings and weekend getaways.',
                'latitude' => 10.7050,
                'longitude' => 124.0080,
                'rating' => 4,
                'address' => 'Guiwanon, Catmon, Cebu',
                'fees' => 'Entrance: ₱30',
                'facilities' => 'Public, Cottages, Restrooms, Parking',
                'cottage' => 'Small ₱400, Large ₱1,200',
            ],
            [
                'name' => 'CATMON WHITE SAND',
                'description' => 'Beautiful white sand beach with clear blue waters. Popular spot for swimming and picnics.',
                'latitude' => 10.7120,
                'longitude' => 124.0120,
                'rating' => 4,
                'address' => 'Catmon, Cebu',
                'fees' => 'Entrance: Free',
                'facilities' => 'Public, White Sand, Swimming Area, Parking',
                'cottage' => 'Small ₱300, Large ₱800',
            ],
            [
                'name' => 'SAN ROQUE BEACH',
                'description' => 'Local beach with fresh seafood stalls and calm swimming areas. Great for experiencing local culture.',
                'latitude' => 10.7200,
                'longitude' => 123.9980,
                'rating' => 3,
                'address' => 'San Roque, Catmon, Cebu',
                'fees' => 'Entrance: Free',
                'facilities' => 'Public, Food Stalls, Fishing Area, Parking',
                'cottage' => 'Small ₱250, Large ₱600',
            ],
            [
                'name' => 'HINAGDAN BEACH RESORT',
                'description' => 'Local beach resort in Binongkalan area with calm waters and relaxing atmosphere.',
                'latitude' => 10.7010,
                'longitude' => 124.0100,
                'rating' => 4,
                'address' => 'Binongkalan, Catmon, Cebu',
                'fees' => 'Entrance: ₱40',
                'facilities' => 'Resort, Cottages, Swimming Area, Parking',
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
                'description' => 'Scenic beach spot in Binongkalan known for occasional turtle sightings and snorkeling.',
                'latitude' => 10.6950,
                'longitude' => 124.0180,
                'rating' => 4,
                'address' => 'Binongkalan, Catmon, Cebu',
                'fees' => 'Entrance: ₱50, Snorkeling: ₱100',
                'facilities' => 'Resort, Snorkel Area, Cottages, Restrooms, Parking, Snorkel Gear',
                'cottage' => 'Small ₱500, Large ₱1,500',
            ],
        ];

        foreach ($nearbyBeaches as $beach) {
            Location::create($beach);
        }
    }
}
