<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactMessage;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $messages = [
            [
                'name' => 'Sarah Mitchell',
                'email' => 'sarah.mitchell@example.com',
                'subject' => 'Volunteer Opportunity Inquiry',
                'message' => 'I am very interested in volunteering for your tree planting initiatives. I have experience in community organizing and would love to contribute to your environmental efforts in Kabul. Please let me know how I can get involved.',
                'created_at' => '2024-03-20 10:30:00',
            ],
            [
                'name' => 'Ahmad Rahman',
                'email' => 'ahmad.rahman@example.com',
                'subject' => 'Partnership Proposal',
                'message' => 'Our company would like to propose a partnership for your upcoming tree planting events. We can provide both financial support and employee volunteers. Please contact me to discuss potential collaboration opportunities.',
                'created_at' => '2024-04-15 14:20:00',
            ],
            [
                'name' => 'Fatima Noori',
                'email' => 'fatima.noori@example.com',
                'subject' => 'School Tree Planting Program',
                'message' => 'I am a teacher at a local school and would like to inquire about incorporating your tree planting program into our curriculum. Our students are very enthusiastic about environmental conservation.',
                'created_at' => '2024-05-10 09:15:00',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'subject' => 'International Support',
                'message' => 'I have been following your work in Kabul and am very impressed with your environmental initiatives. I would like to make a donation to support your tree planting efforts. Please provide information on how international supporters can contribute.',
                'created_at' => '2024-06-05 16:45:00',
            ],
            [
                'name' => 'Laila Hassani',
                'email' => 'laila.hassani@example.com',
                'subject' => 'Tree Species Information',
                'message' => 'I am interested in learning more about the native tree species you are planting in Kabul. Do you have educational materials or resources about the ecological benefits of these species?',
                'created_at' => '2024-07-12 11:30:00',
            ],
            [
                'name' => 'David Thompson',
                'email' => 'david.thompson@example.com',
                'subject' => 'Media Coverage Request',
                'message' => 'I am a journalist working on an article about environmental initiatives in Afghanistan. I would like to feature your organization and tree planting projects. Would it be possible to schedule an interview?',
                'created_at' => '2024-08-18 13:00:00',
            ],
            [
                'name' => 'Zarifa Karimi',
                'email' => 'zarifa.karimi@example.com',
                'subject' => 'Community Garden Support',
                'message' => 'Our community would like to start a garden similar to the one featured in your recent project. Could you provide guidance on how to begin and what resources we would need?',
                'created_at' => '2024-09-22 10:15:00',
            ],
            [
                'name' => 'Robert Williams',
                'email' => 'robert.williams@example.com',
                'subject' => 'Technical Assistance Offer',
                'message' => 'I am an agricultural engineer specializing in arid climate tree cultivation. I would like to offer my technical expertise to help optimize your tree planting methods for Kabul\'s climate conditions.',
                'created_at' => '2024-10-15 15:30:00',
            ],
            [
                'name' => 'Amina Ghani',
                'email' => 'amina.ghani@example.com',
                'subject' => 'Youth Environmental Club',
                'message' => 'I want to start an environmental club at my university inspired by your work. Could you provide advice on organizing student-led tree planting activities and environmental awareness campaigns?',
                'created_at' => '2024-11-20 08:45:00',
            ],
            [
                'name' => 'James Foster',
                'email' => 'james.foster@example.com',
                'subject' => 'Grant Application Support',
                'message' => 'Our foundation provides grants for environmental projects in developing regions. I would like to learn more about your organization to consider you for our next funding cycle. Do you have a formal proposal or project plan?',
                'created_at' => '2024-12-10 12:00:00',
            ],
        ];

        foreach ($messages as $message) {
            ContactMessage::create($message);
        }
    }
}
