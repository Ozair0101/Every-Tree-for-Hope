<?php

namespace Database\Seeders;

use App\Models\Donator;
use Illuminate\Database\Seeder;

class DonatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $donators = [
            [
                'full_name' => 'Abdul Karim Khan',
                'impact' => 'Sponsored 50 trees for Kabul Green Belt Initiative',
                'location' => 'Kabul, Afghanistan',
                'financial_support' => 2500.00,
                'status' => 'verified',
                'phone' => '+93 70 123 4567',
                'profile_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-03-15',
            ],
            [
                'full_name' => 'Fatima Rahimi',
                'impact' => 'Contributed to School Tree Planting Drive',
                'location' => 'Herat, Afghanistan',
                'financial_support' => 1200.00,
                'status' => 'verified',
                'phone' => '+93 40 234 5678',
                'profile_image' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-04-20',
            ],
            [
                'full_name' => 'Ahmad Hassan Zai',
                'impact' => 'Supported Riverside Restoration Project',
                'location' => 'Mazar-i-Sharif, Afghanistan',
                'financial_support' => 3500.00,
                'status' => 'verified',
                'phone' => '+93 60 345 6789',
                'profile_image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-05-10',
            ],
            [
                'full_name' => 'Mariam Yousufi',
                'impact' => 'Contributed to Urban Parks Greening',
                'location' => 'Kandahar, Afghanistan',
                'financial_support' => 800.00,
                'status' => 'verified',
                'phone' => '+93 50 456 7890',
                'profile_image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-06-05',
            ],
            [
                'full_name' => 'Mohammad Daoud',
                'impact' => 'Mountain Slope Stabilization Support',
                'location' => 'Jalalabad, Afghanistan',
                'financial_support' => 2000.00,
                'status' => 'verified',
                'phone' => '+93 30 567 8901',
                'profile_image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-07-12',
            ],
            [
                'full_name' => 'Sakina Ghani',
                'impact' => 'Community Garden Initiative Support',
                'location' => 'Kabul, Afghanistan',
                'financial_support' => 600.00,
                'status' => 'verified',
                'phone' => '+93 70 678 9012',
                'profile_image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-08-18',
            ],
            [
                'full_name' => 'John Smith',
                'impact' => 'Corporate Tree Planting Day Sponsor',
                'location' => 'New York, USA',
                'financial_support' => 5000.00,
                'status' => 'verified',
                'phone' => '+1 555 0123 4567',
                'profile_image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-09-22',
            ],
            [
                'full_name' => 'Sarah Johnson',
                'impact' => 'Memorial Forest Project Contribution',
                'location' => 'London, UK',
                'financial_support' => 1500.00,
                'status' => 'verified',
                'phone' => '+44 20 7123 4567',
                'profile_image' => 'https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-10-15',
            ],
            [
                'full_name' => 'Ali Mohammad',
                'impact' => 'Winter Tree Protection Campaign',
                'location' => 'Ghazni, Afghanistan',
                'financial_support' => 400.00,
                'status' => 'verified',
                'phone' => '+93 35 789 0123',
                'profile_image' => 'https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-11-20',
            ],
            [
                'full_name' => 'Zarifa Azimi',
                'impact' => 'Spring Blossom Festival Support',
                'location' => 'Bamyan, Afghanistan',
                'financial_support' => 900.00,
                'status' => 'verified',
                'phone' => '+93 55 890 1234',
                'profile_image' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2025-03-25',
            ],
            [
                'full_name' => 'Green Earth Foundation',
                'impact' => 'Major sponsor for Kabul Green Belt Initiative',
                'location' => 'Dubai, UAE',
                'financial_support' => 10000.00,
                'status' => 'verified',
                'phone' => '+971 4 123 4567',
                'profile_image' => 'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-03-10',
            ],
            [
                'full_name' => 'Education Ministry',
                'impact' => 'School Tree Planting Drive Partner',
                'location' => 'Kabul, Afghanistan',
                'financial_support' => 7500.00,
                'status' => 'verified',
                'phone' => '+93 20 123 4567',
                'profile_image' => 'https://images.unsplash.com/photo-1515186379248-b5012850c69b?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-04-15',
            ],
            [
                'full_name' => 'Water Conservation Authority',
                'impact' => 'Riverside Restoration Project Sponsor',
                'location' => 'Kabul, Afghanistan',
                'financial_support' => 6000.00,
                'status' => 'verified',
                'phone' => '+93 20 234 5678',
                'profile_image' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-05-05',
            ],
            [
                'full_name' => 'Emma Wilson',
                'impact' => 'Individual supporter for community gardens',
                'location' => 'Sydney, Australia',
                'financial_support' => 1800.00,
                'status' => 'verified',
                'phone' => '+61 2 9876 5432',
                'profile_image' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-08-20',
            ],
            [
                'full_name' => 'Khalid Noorzai',
                'impact' => 'Urban parks and recreational spaces supporter',
                'location' => 'Kabul, Afghanistan',
                'financial_support' => 1100.00,
                'status' => 'verified',
                'phone' => '+93 70 345 6789',
                'profile_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
                'donation_date' => '2024-06-10',
            ],
        ];

        foreach ($donators as $donator) {
            Donator::create($donator);
        }
    }
}
