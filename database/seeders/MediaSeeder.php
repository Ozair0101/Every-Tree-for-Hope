<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Media;

class MediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mediaItems = [
            [
                'title' => 'Kabul Green Belt Initiative Launch',
                'description' => 'Official launch ceremony of the Kabul Green Belt Initiative with government officials and community leaders.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-03-16',
                'is_active' => true,
            ],
            [
                'title' => 'Students Planting Trees at Kabul Schools',
                'description' => 'Young students actively participating in the School Tree Planting Drive, learning about environmental conservation.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-04-21',
                'is_active' => true,
            ],
            [
                'title' => 'Riverside Restoration Progress',
                'description' => 'Significant progress in Kabul River restoration project with newly planted willow trees taking root.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-05-11',
                'is_active' => true,
            ],
            [
                'title' => 'Zarnegar Park Transformation',
                'description' => 'Before and after view of Zarnegar Park transformation through urban greening efforts.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-06-06',
                'is_active' => true,
            ],
            [
                'title' => 'Mountain Slope Stabilization Work',
                'description' => 'Team working on mountain slope stabilization using deep-rooted native tree species.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-07-13',
                'is_active' => true,
            ],
            [
                'title' => 'Community Garden Harvest',
                'description' => 'First harvest from the Khair Khana community garden project showing successful vegetable growth.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-08-19',
                'is_active' => true,
            ],
            [
                'title' => 'Corporate Volunteers in Action',
                'description' => 'Employees from various companies participating in the Corporate Tree Planting Day.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-09-23',
                'is_active' => true,
            ],
            [
                'title' => 'Memorial Forest Opening Ceremony',
                'description' => 'Emotional opening ceremony of the Memorial Forest Project with families participating.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-10-16',
                'is_active' => true,
            ],
            [
                'title' => 'Winter Tree Protection Measures',
                'description' => 'Volunteers applying protective measures to young trees for winter survival.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-11-21',
                'is_active' => true,
            ],
            [
                'title' => 'Spring Blossom Festival Highlights',
                'description' => 'Colorful highlights from the annual Spring Blossom Festival at Babur Gardens.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2025-03-26',
                'is_active' => true,
            ],
            [
                'title' => 'Environmental Education Workshop',
                'description' => 'Interactive workshop teaching children about the importance of trees and environmental conservation.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-04-22',
                'is_active' => true,
            ],
            [
                'title' => 'Volunteer Training Session',
                'description' => 'Comprehensive training session for new volunteers on proper tree planting techniques.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-03-14',
                'is_active' => true,
            ],
            [
                'title' => 'Tree Nursery Development',
                'description' => 'New tree nursery setup to grow saplings specifically adapted to Kabul\'s climate.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-02-28',
                'is_active' => true,
            ],
            [
                'title' => 'Community Meeting on Urban Greening',
                'description' => 'Town hall meeting with community members discussing urban greening initiatives.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-05-08',
                'is_active' => true,
            ],
            [
                'title' => 'First Year Anniversary Celebration',
                'description' => 'Celebrating one year of successful tree planting initiatives with all stakeholders.',
                'video_youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'date' => '2024-12-15',
                'is_active' => true,
            ],
        ];

        foreach ($mediaItems as $media) {
            Media::create($media);
        }
    }
}
