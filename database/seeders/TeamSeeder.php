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
                'image' => 'team-photos/tamim.jpeg',
                'bio' => 'Environmental visionary with over 15 years of experience in sustainable development and community engagement.',
                'message' => 'Every tree planted is a promise kept to the future generations of Kabul. We aren\'t just planting wood and leaves; we are planting hope.',
                'is_active' => true,
            ],
            [
                'name' => 'Mohammad Iqbal Alimyar',
                'position' => 'Sustainability Advisor',
                'image' => 'team-photos/Iqbal.jpeg',
                'bio' => 'Specialized in native Afghan flora and ecosystem restoration with a PhD in Environmental Science.',
                'message' => 'Nature has its own wisdom; we just need to listen and learn from it.',
                'is_active' => true,
            ],
            [
                'name' => 'Mohammad Edris Alimyar',
                'position' => 'Media & outreach coordianotr',
                'image' => 'team-photos/edris.jpeg',
                'bio' => 'Community organizer passionate about bringing people together for environmental causes.',
                'message' => 'When communities unite, mountains can be moved and forests can grow.',
                'is_active' => true,
            ],
            [
                'name' => 'Mohammad Ozair Khurami',
                'position' => 'Website Manager & Developer',
                'image' => 'team-photos/ozair.JPG',
                'bio' => 'Efficient operations manager ensuring smooth execution of all tree planting initiatives.',
                'message' => 'Behind every successful tree planted is a team of dedicated people working seamlessly.',
                'is_active' => true,
            ],
            [
                'name' => 'Anel Ahmad Kaker',
                'position' => 'Co_Leader Coordinator',
                'image' => 'team-photos/anel.jpeg',
                'bio' => 'Botanical expert specializing in drought-resistant species suitable for Kabul\'s climate.',
                'message' => 'The right tree in the right place can change everything.',
                'is_active' => true,
            ],
            [
                'name' => 'Fazel Ahmad',
                'position' => 'Community & Partnerships Coordinator',
                'image' => 'team-photos/fazel.jpeg',
                'email' => 'fatima@everytreeforhope.org',
                'bio' => 'Research scientist focusing on air quality improvement through urban forestry.',
                'message' => 'Science tells us trees are the answer to many of our environmental challenges.',
                'is_active' => true,
            ],
            [
                'name' => 'Ekramuden Jami',
                'position' => 'Project Coordinator',
                'image' => 'team-photos/ekram.jpeg',
                'bio' => 'Experienced project manager coordinating large-scale tree planting across Kabul.',
                'message' => 'Every project starts with a single seed and grows into a forest of possibilities.',
                'is_active' => true,
            ],
            [
                'name' => 'Edris Ahmad Mirkhail',
                'position' => 'Enviromental & Technical Advisor',
                'image' => 'team-photos/aqwqwq.jpg.jpeg',
                'bio' => 'Passionate about mobilizing volunteers and building community engagement programs.',
                'message' => 'Volunteers are the roots that anchor our organization in the community.',
                'is_active' => true,
            ],
            [
                'name' => 'Ali Ramen Mahmodi',
                'position' => 'StoryTelling & Narrative Lead',
                'image' => 'team-photos/ali.jpeg',
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
