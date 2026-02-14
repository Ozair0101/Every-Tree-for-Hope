<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Partner;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'company_name' => 'Green Earth Foundation',
                'logo' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'company_name' => 'Education Ministry',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'company_name' => 'Water Conservation Authority',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'company_name' => 'Municipality of Kabul',
                'logo' => 'https://images.unsplash.com/photo-1515186379248-b5012850c69b?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'company_name' => 'Disaster Management Authority',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'company_name' => 'Afghan Telecom',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'company_name' => 'Cultural Heritage Foundation',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 7,
            ],
            [
                'company_name' => 'Seasonal Care Initiative',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 8,
            ],
            [
                'company_name' => 'Cultural Affairs Ministry',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 9,
            ],
            [
                'company_name' => 'Local Community Council',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'company_name' => 'UN Environment Programme',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 11,
            ],
            [
                'company_name' => 'World Wildlife Fund',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 12,
            ],
            [
                'company_name' => 'Global Green Growth Institute',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 13,
            ],
            [
                'company_name' => 'Afghan Red Crescent Society',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 14,
            ],
            [
                'company_name' => 'International Rescue Committee',
                'logo' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=200&h=100&fit=crop',
                'is_active' => true,
                'sort_order' => 15,
            ],
        ];

        foreach ($partners as $partner) {
            Partner::create($partner);
        }
    }
}
