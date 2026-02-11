<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventImage;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = [
            [
                'title' => 'Kabul Green Belt Initiative',
                'description' => 'A massive tree planting initiative to create a green belt around Kabul city, involving over 500 volunteers and planting 2,000 native trees including pine, oak, and maple species.',
                'location' => 'Kabul City Outskirts',
                'date' => '2024-03-15',
                'trees_planted' => 2000,
                'volunteers' => 500,
                'sponsor_partner' => 'Green Earth Foundation',
                'is_active' => true,
                'sort_order' => 1,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1540206395-68808572332f?w=800&h=600&fit=crop',
                        'caption' => 'Volunteers planting saplings in Kabul outskirts',
                    ],
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&h=600&fit=crop',
                        'caption' => 'Community gathering before tree planting',
                    ],
                ],
            ],
            [
                'title' => 'School Tree Planting Drive',
                'description' => 'Educational initiative involving 15 schools across Kabul, teaching students about environmental conservation while planting fruit trees in school grounds.',
                'location' => 'Various Schools, Kabul',
                'date' => '2024-04-20',
                'trees_planted' => 450,
                'volunteers' => 300,
                'sponsor_partner' => 'Education Ministry',
                'is_active' => true,
                'sort_order' => 2,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=800&h=600&fit=crop',
                        'caption' => 'Students planting trees in school grounds',
                    ],
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800&h=600&fit=crop',
                        'caption' => 'Environmental education session',
                    ],
                ],
            ],
            [
                'title' => 'Riverside Restoration Project',
                'description' => 'Restoration of Kabul River banks by planting native willow and poplar trees to prevent erosion and improve water quality.',
                'location' => 'Kabul River Banks',
                'date' => '2024-05-10',
                'trees_planted' => 800,
                'volunteers' => 200,
                'sponsor_partner' => 'Water Conservation Authority',
                'is_active' => true,
                'sort_order' => 3,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1540206395-68808572332f?w=800&h=600&fit=crop',
                        'caption' => 'Riverside tree planting activity',
                    ],
                ],
            ],
            [
                'title' => 'Urban Parks Greening',
                'description' => 'Beautification of urban parks in Kabul by planting ornamental trees and creating green spaces for community recreation.',
                'location' => 'Zarnegar Park, Kabul',
                'date' => '2024-06-05',
                'trees_planted' => 350,
                'volunteers' => 150,
                'sponsor_partner' => 'Municipality of Kabul',
                'is_active' => true,
                'sort_order' => 4,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=800&h=600&fit=crop',
                        'caption' => 'Urban park transformation',
                    ],
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1516214104703-d870798faf8f?w=800&h=600&fit=crop',
                        'caption' => 'Community volunteers at work',
                    ],
                ],
            ],
            [
                'title' => 'Mountain Slope Stabilization',
                'description' => 'Planting deep-rooted trees on mountain slopes around Kabul to prevent landslides and soil erosion during rainy seasons.',
                'location' => 'Asamayi Mountain, Kabul',
                'date' => '2024-07-12',
                'trees_planted' => 1200,
                'volunteers' => 250,
                'sponsor_partner' => 'Disaster Management Authority',
                'is_active' => true,
                'sort_order' => 5,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&h=600&fit=crop',
                        'caption' => 'Mountain slope tree planting',
                    ],
                ],
            ],
            [
                'title' => 'Community Garden Initiative',
                'description' => 'Establishing community gardens in residential areas, planting fruit trees and vegetables to promote food security and community bonding.',
                'location' => 'Khair Khana District, Kabul',
                'date' => '2024-08-18',
                'trees_planted' => 180,
                'volunteers' => 120,
                'sponsor_partner' => 'Local Community Council',
                'is_active' => true,
                'sort_order' => 6,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?w=800&h=600&fit=crop',
                        'caption' => 'Community garden setup',
                    ],
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=800&h=600&fit=crop',
                        'caption' => 'Families working together in garden',
                    ],
                ],
            ],
            [
                'title' => 'Corporate Tree Planting Day',
                'description' => 'Corporate social responsibility event where local companies participated in planting trees and donating to environmental causes.',
                'location' => 'Wazir Akbar Khan, Kabul',
                'date' => '2024-09-22',
                'trees_planted' => 600,
                'volunteers' => 180,
                'sponsor_partner' => 'Afghan Telecom',
                'is_active' => true,
                'sort_order' => 7,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=800&h=600&fit=crop',
                        'caption' => 'Corporate volunteers planting trees',
                    ],
                ],
            ],
            [
                'title' => 'Memorial Forest Project',
                'description' => 'Creating a memorial forest where families can plant trees in memory of loved ones, combining environmental conservation with cultural traditions.',
                'location' => 'Kabul Memorial Gardens',
                'date' => '2024-10-15',
                'trees_planted' => 300,
                'volunteers' => 100,
                'sponsor_partner' => 'Cultural Heritage Foundation',
                'is_active' => true,
                'sort_order' => 8,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=800&h=600&fit=crop',
                        'caption' => 'Memorial tree planting ceremony',
                    ],
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1501854140801-50d01698950b?w=800&h=600&fit=crop',
                        'caption' => 'Families participating in memorial planting',
                    ],
                ],
            ],
            [
                'title' => 'Winter Tree Protection Campaign',
                'description' => 'Protecting young trees during harsh winter months by providing proper covering and care to ensure survival through the cold season.',
                'location' => 'Various Locations, Kabul',
                'date' => '2024-11-20',
                'trees_planted' => 0,
                'volunteers' => 80,
                'sponsor_partner' => 'Seasonal Care Initiative',
                'is_active' => true,
                'sort_order' => 9,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1540206395-68808572332f?w=800&h=600&fit=crop',
                        'caption' => 'Winter tree protection activities',
                    ],
                ],
            ],
            [
                'title' => 'Spring Blossom Festival',
                'description' => 'Annual spring festival celebrating the blooming of planted trees, featuring community gatherings, educational workshops, and new tree planting ceremonies.',
                'location' => 'Babur Gardens, Kabul',
                'date' => '2025-03-25',
                'trees_planted' => 150,
                'volunteers' => 200,
                'sponsor_partner' => 'Cultural Affairs Ministry',
                'is_active' => true,
                'sort_order' => 10,
                'images' => [
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1490750967868-88aa4486c946?w=800&h=600&fit=crop',
                        'caption' => 'Spring blossom celebration',
                    ],
                    [
                        'image_path' => 'https://images.unsplash.com/photo-1516214104703-d870798faf8f?w=800&h=600&fit=crop',
                        'caption' => 'Community festival activities',
                    ],
                ],
            ],
        ];

        foreach ($events as $eventData) {
            $images = $eventData['images'];
            unset($eventData['images']);

            $event = Event::create($eventData);

            foreach ($images as $imageData) {
                $imageData['event_id'] = $event->id;
                EventImage::create($imageData);
            }
        }
    }
}
