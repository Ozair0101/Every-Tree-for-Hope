<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            // Introduction and Background
            [
                'category' => 'Introduction & Background',
                'question' => 'How and by whom was this group formed?',
                'answer' => 'I, "Tamim Alimyar", with my family and brother "Mohammad Iqbal Alimyar", used to watch the nature tourism programs of Imran Vadan. My brother Iqbal would always criticize Imran Vadan for not planting a tree every time he travels to areas with abundant water resources. I told my brother: "Let\'s do something. Imran showed us the areas and you plant the tree." And in this way, Every Tree for a Hope was formed. I prepared the design and the work plan, shared it with my friends, and it was approved and supported by them. The next day, we started from Haji Nabi town and planted 12 trees on vast hills where no trace of a tree was visible.',
                'sort_order' => 1,
            ],
            [
                'category' => 'Introduction & Background',
                'question' => 'What was your main motivation for starting the "Every Tree for a Hope" project?',
                'answer' => 'Afghanistan is a country with abundant water resources and a suitable climate, but little work had been done in the area of environment and greenery. People had completely forgotten the environment. Every day the air in Kabul becomes more polluted, green areas are turned into high-rise buildings, trees are cut down, the soil is very windy, temperatures are rising, and groundwater levels are falling. Why are neighboring countries greener than Afghanistan despite not having water? What we can do is create a culture of tree planting and environmental protection — something forgotten with each passing day. If every family plants a tree in front of their house and takes care of it, Afghanistan will have green space.',
                'sort_order' => 2,
            ],
            [
                'category' => 'Introduction & Background',
                'question' => 'Why did you decide to start this work voluntarily?',
                'answer' => 'To create a culture of charity and volunteer work. The tree is a public benefit — its oxygen, shade, and fruit reach all humans, birds, and animals. Religiously, tree planting is a running charity (Sadaqah Jariyah). Anyone who plants a tree and others use its oxygen, shade or fruit has actually given charity. We had to promote a sense of patriotism and charity. I explained the value of the tree to my friends and convinced them that the least we can do for our country is to plant saplings that will last for generations.',
                'sort_order' => 3,
            ],

            // Project Goals and Mission
            [
                'category' => 'Goals & Mission',
                'question' => 'What is the short-term and long-term goal of this project?',
                'answer' => 'Short-term: raise awareness among people about the big change trees can bring. Convey our message and increase the sense of patriotism among all citizens. Long-term: we want every Afghan to plant at least one tree in front of their house. Afghanistan has about 35 million people. If just 3 million plant a tree, imagine what a big change that would be in one year. We are confident we will achieve this goal.',
                'sort_order' => 4,
            ],
            [
                'category' => 'Goals & Mission',
                'question' => 'Why is the greenery of Kabul and Afghanistan especially important to you?',
                'answer' => 'Kabul is the center of Afghanistan with a population of more than 10 million. It should be a leader in cleanliness, greenery, and culture. I have traveled to Iran and Pakistan and seen how much their people care about the environment, but in Afghanistan children play on dusty ground and in some areas it\'s difficult to find a tree to sit under. In Kabul there are rarely green parks, and birds are becoming fewer every day because their growth environment is not available.',
                'sort_order' => 5,
            ],
            [
                'category' => 'Goals & Mission',
                'question' => 'How do you want to institutionalize the culture of tree planting in society?',
                'answer' => 'Through education and comparing barren areas with green areas. Through the emphasis of Islam on the importance of trees and the history of trees in Afghan culture. By emphasizing the importance of green space and clean air, and showing how trees absorb water and provide moisture to the area.',
                'sort_order' => 6,
            ],

            // Working Methods
            [
                'category' => 'Working Methods',
                'question' => 'What are your working methods and field programs for planting trees?',
                'answer' => 'I prepare the work plan two weeks in advance and share it with friends — survey this week, see the trees on a certain day, watering on a certain day. I share it via WhatsApp, everyone votes on who is coming. I use my personal car to take everyone to the field. We have good teammates who are highly motivated. On days when I am lazy, they motivate me to move forward.',
                'sort_order' => 7,
            ],
            [
                'category' => 'Working Methods',
                'question' => 'What types of trees do you plant and why?',
                'answer' => 'We propagate native trees of Afghanistan and save them from extinction — purple, mulberry, sheng, and najo. The Russian willow has covered about 60% of the country and destroyed native trees. Our goal is not only to increase the number of trees but to beautify the city with useful trees. We are cultivating the oak tree — a paradise tree that is a nest for birds and animals. We requested 3 kilos of oak seeds from Iran, and the Iranian brothers are cooperating with us.',
                'sort_order' => 8,
            ],
            [
                'category' => 'Working Methods',
                'question' => 'What is your plan for maintaining and watering the trees?',
                'answer' => 'We use the drip irrigation method with 5-liter bottles, which works well. Our model is: we plant trees and the local people take care of them. Wherever there is a need, the team provides and plants a tree. Watering is the responsibility of the local community. When they don\'t cooperate, we provide water with tankers or buckets from the project budget.',
                'sort_order' => 9,
            ],

            // Team & Structure
            [
                'category' => 'Team & Structure',
                'question' => 'Have families, local elders, or schools cooperated with you?',
                'answer' => 'Yes! The first support came from my brother Mohammad Iqbal Alimyar, and friends Anil Ahmad Kakar, Idris Ahmad Mirkhel, Mohammad Idris Alimyar, and Ali Ramin Mahmoudi. My father, Mr. Mohammad Ehsan Alimyar, was one of the first volunteers who came to the field and planted trees. Later, Ikramuddin Jami, Hamed Soltani, Mushtaq Mukhtar, Wafa Amini, Mustafa Taheri, Zakaria Haidari, and Fazal Ahmad joined. Local people, university professors, and religious leaders all supported us. The most financial support came from Afghan friends in Europe and America.',
                'sort_order' => 10,
            ],
            [
                'category' => 'Team & Structure',
                'question' => 'How many people are active in this group?',
                'answer' => 'We have 10 active key members. Plus around 20 volunteers from inside and outside the country who openly cooperate and help us.',
                'sort_order' => 11,
            ],
            [
                'category' => 'Team & Structure',
                'question' => 'How are duties and roles divided among members?',
                'answer' => 'I, Tamim Alimyar, lead the project. Colleagues are responsible for financial, survey, filming, purchasing trees, and communications sectors. If a volunteer cannot carry out their activity, I cover them as project leader. We assigned roles based on qualifications: 1 civil engineer, 3 computer engineers, 2 economists, and 4 medical and computer engineering students.',
                'sort_order' => 12,
            ],
            [
                'category' => 'Team & Structure',
                'question' => 'Where do your financial and logistical resources come from?',
                'answer' => 'For the first two months, all members financed from personal budgets — we bought a shovel, a spade, and 12 tree seeds. Each member contributed 500 Afghanis from their pockets. After two months, we received support from inside and outside the country. The first major support was 12,000 Afghanis from a big-hearted girl in Germany, which significantly increased our tree count. Our brother Qudratullah Ahmadi funded our vests and clothes for organization.',
                'sort_order' => 13,
            ],

            // Impact & Community
            [
                'category' => 'Impact & Community',
                'question' => 'How many seedlings have you planted since the beginning?',
                'answer' => 'We were fortunate to plant 142 trees during the initial phase. These include mulberry, arghawan, divar bid, najo, sheng, and mad bid. Today the number has grown to over 1,000 trees across multiple locations.',
                'sort_order' => 14,
            ],
            [
                'category' => 'Impact & Community',
                'question' => 'Which locations have been most involved in your campaign?',
                'answer' => 'The hills of Haji Nabi town, Sadat Hofiani Mosque, Qargha Dam, Sakhi Shrine, and Karte Se area. Our dream is still to make those Haji Nabi hills green. We hope the government will provide water so we can turn them into a green and pleasant park.',
                'sort_order' => 15,
            ],
            [
                'category' => 'Impact & Community',
                'question' => 'How have the people and community reacted to this project?',
                'answer' => 'No one has said tree planting is bad. Most supported us verbally, and increasingly in practice too. Our biggest supporters were the caring sisters and mothers who helped with their meager income — they held our hands in difficult situations. People\'s views have changed: everyone sees our work with respect. Afghans abroad coordinated with us and found the best moments of their trips in this project.',
                'sort_order' => 16,
            ],

            // Future & Vision
            [
                'category' => 'Future & Vision',
                'question' => 'What is your plan for developing activities beyond Kabul?',
                'answer' => 'We started from Kabul for easier access to services and volunteers. After a few more planting sessions, we will go to nearby provinces — Maidan Wardak, Bamyan, and Ghazni. Slowly and steadily, we will reach all 34 provinces. Friends from Herat and Sar-e-Pul already sent messages wanting to take action in their own provinces with our support.',
                'sort_order' => 17,
            ],
            [
                'category' => 'Future & Vision',
                'question' => 'How do you see "Every Tree for a Hope" in the next five years?',
                'answer' => 'In 5 years: thousands of trees planted, oak trees multiplied throughout the country, bird populations recovering, Kabul\'s pollution improving, diseases decreasing, green areas increasing. Our message will reach every corner of the world. With the trees we plant, the blessing in the land will increase, humidity in the air will increase, and we will see more rainfall. No dam in Kabul or Afghanistan will be left without trees.',
                'sort_order' => 18,
            ],
            [
                'category' => 'Future & Vision',
                'question' => 'Do you have plans for environmental education in schools?',
                'answer' => 'Yes! Our team\'s knowledge about environment and trees grows every day. We want to hold seminars with schools, colleges, and universities, both domestic and foreign. We\'ve already held scientific and comparative seminars. We prepared a storybook for children so they know the value of trees from childhood, and we plan to distribute it in kindergartens. In winter when we cannot plant, we focus on educational programs.',
                'sort_order' => 19,
            ],

            // Challenges
            [
                'category' => 'Challenges',
                'question' => 'What challenges have you experienced in implementing the project?',
                'answer' => 'On the Haji Nabi hills, water shortage was a problem, and local authorities restricted us from planting more trees to prevent land disputes. We planted 62 trees there before being told to stop. Teenagers cut down some trees and stole our drip irrigation bottles, which was disappointing. But most local people gave us tea, shoveled with us, and came to help. We adapted — surveyed mosques, Sakhi Shrine, and Qargha Dam where people take responsibility for watering.',
                'sort_order' => 20,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create(array_merge($faq, [
                'is_verified' => true,
                'asked_by_name' => 'Tamim Alimyar',
            ]));
        }
    }
}
