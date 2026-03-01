<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teamMembers = [
            [
                'name' => 'Tamim Alimyar',
                'position' => 'Founder Project Leader',
                'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop&crop=face',
                'bio' => 'Environmental visionary with over 15 years of experience in sustainable development and community engagement.',
                'message' => 'Every tree planted is a promise kept to the future generations of Kabul. We aren\'t just planting wood and leaves; we are planting hope.',
                'is_active' => true,
            ],
            [
                'name' => 'Mohammad Iqbal Alimyar',
                'position' => 'Sustainability Advisor',
                'image' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop&crop=face',
                'bio' => 'Specialized in native Afghan flora and ecosystem restoration with a PhD in Environmental Science.',
                'message' => 'Nature has its own wisdom; we just need to listen and learn from it.',
                'is_active' => true,
            ],
            [
                'name' => 'Mohammad Edris Alimyar',
                'position' => 'Media & outreach coordianotr',
                'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop&crop=face',
                'bio' => 'Community organizer passionate about bringing people together for environmental causes.',
                'message' => 'When communities unite, mountains can be moved and forests can grow.',
                'is_active' => true,
            ],
            [
                'name' => 'Mohammad Ozair Khurami',
                'position' => 'Website Manager & Developer',
                'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop&crop=face',
                'email' => 'ozairjan186@gmail.com',
                'bio' => 'Efficient operations manager ensuring smooth execution of all tree planting initiatives.',
                'message' => 'Behind every successful tree planted is a team of dedicated people working seamlessly.',
                'is_active' => true,
            ],
            [
                'name' => 'Anel Ahmad Kaker',
                'position' => 'Co_Leader Coordinator',
                'image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&h=400&fit=crop&crop=face',
                'bio' => 'Botanical expert specializing in drought-resistant species suitable for Kabul\'s climate.',
                'message' => 'The right tree in the right place can change everything.',
                'is_active' => true,
            ],
            [
                'name' => 'Fazel Ahmad',
                'position' => 'Community & Partnerships Coordinator',
                'image' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&h=400&fit=crop&crop=face',
                'social_media_url' => 'https://linkedin.com/in/fatimakarzai',
                'email' => 'fatima@everytreeforhope.org',
                'bio' => 'Research scientist focusing on air quality improvement through urban forestry.',
                'message' => 'Science tells us trees are the answer to many of our environmental challenges.',
                'is_active' => true,
            ],
            [
                'name' => 'Ekramuden Jami',
                'position' => 'Project Coordinator',
                'image' => 'https://images.unsplash.com/photo-1507591064344-4c6ce005b128?w=400&h=400&fit=crop&crop=face',
                'bio' => 'Experienced project manager coordinating large-scale tree planting across Kabul.',
                'message' => 'Every project starts with a single seed and grows into a forest of possibilities.',
                'is_active' => true,
            ],
            [
                'name' => 'Aisha Mohammadi',
                'position' => 'Volunteer Coordinator',
                'image' => 'https://images.unsplash.com/photo-1489424731084-a5d8b219a5bb?w=400&h=400&fit=crop&crop=face',
                'social_media_url' => 'https://instagram.com/aishamohammadi',
                'email' => 'aisha@everytreeforhope.org',
                'bio' => 'Passionate about mobilizing volunteers and building community engagement programs.',
                'message' => 'Volunteers are the roots that anchor our organization in the community.',
                'is_active' => true,
            ],
        ];

        foreach ($teamMembers as $member) {
            Team::create($member);
        }
    }
}
