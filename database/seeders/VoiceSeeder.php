<?php

namespace Database\Seeders;

use App\Models\Voice;
use App\Models\VoiceComment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VoiceSeeder extends Seeder
{
    public function run(): void
    {
        $voices = [
            [
                'author_name' => 'Mariam Noori',
                'country' => 'Afghanistan',
                'category' => 'experience',
                'title' => 'The day our barren hillside turned green again',
                'body' => "Three years ago the hill behind our village in Bamyan was bare rock and dust. Our community decided to plant almond and pistachio saplings, watering them by hand every evening.\n\nThis spring, for the first time, the whole slope was covered in white blossom. Children who had never seen a tree on that hill now play in its shade. Nature gives back everything we give to it — and more.",
                'likes' => 142,
            ],
            [
                'author_name' => 'David Chen',
                'country' => 'Canada',
                'category' => 'finding',
                'title' => 'I tracked the same maple for 10 years — here is what I learned',
                'body' => "Every autumn I photograph one sugar maple near my home and note the date it turns red. Over ten years the colour change has shifted almost two weeks later in the season.\n\nIt is a tiny, personal data set, but standing in front of one tree year after year taught me more about a changing climate than any headline ever did. Pick a tree. Watch it. It will tell you a story.",
                'likes' => 98,
            ],
            [
                'author_name' => 'Aisha Bello',
                'country' => 'Nigeria',
                'category' => 'idea',
                'title' => 'What if every newborn was gifted a tree?',
                'body' => "In my town we started a small tradition: when a baby is born, the family plants a tree with their name on a wooden tag. The tree and the child grow together.\n\nTwenty years later, that child has a living, breathing companion taller than themselves. Imagine if every community did this. A forest of names, a forest of futures.",
                'likes' => 211,
            ],
            [
                'author_name' => 'Lukas Berg',
                'country' => 'Germany',
                'category' => 'finding',
                'title' => 'Bees came back the year we stopped mowing',
                'body' => "We let one corner of our garden grow wild — no mowing, no cutting. Within a single summer the wildflowers returned, and with them more bees and butterflies than I had seen in a decade.\n\nSometimes the most powerful thing we can do for nature is simply to step back and let it breathe.",
                'likes' => 76,
            ],
            [
                'author_name' => 'Sofia Rossi',
                'country' => 'Italy',
                'category' => 'experience',
                'title' => 'My grandfather\'s olive trees are 400 years old',
                'body' => "The olive trees on our family land in Puglia were planted long before any of us were born. My grandfather used to say we do not own them — we only look after them for the next generation.\n\nStanding among trees older than your country puts your whole life into perspective.",
                'likes' => 187,
            ],
            [
                'author_name' => 'Ravi Patel',
                'country' => 'India',
                'category' => 'question',
                'title' => 'Has anyone tried growing native species instead of fast-growing ones?',
                'body' => "Our city keeps planting fast-growing non-native trees because they look green quickly. But they need a lot of water and the local birds ignore them.\n\nHas anyone here had success convincing their community to plant slower, native species instead? I would love to learn from your experience.",
                'likes' => 54,
            ],
            [
                'author_name' => 'Emma Thompson',
                'country' => 'United Kingdom',
                'category' => 'idea',
                'title' => 'Turn schoolyards into tiny forests',
                'body' => "We helped a local primary school convert an unused asphalt corner into a dense mini-forest using the Miyawaki method. Around 600 native saplings in a space the size of a tennis court.\n\nTwo years on, it is taller than the children and full of insects. The kids run their own 'forest club' now. Small spaces can do enormous things.",
                'likes' => 163,
            ],
            [
                'author_name' => 'Khalid Hassan',
                'country' => 'Egypt',
                'category' => 'finding',
                'title' => 'Date palms are quietly cooling our streets',
                'body' => "We measured the temperature under our row of date palms versus the open street nearby. The shaded path was consistently 6–8°C cooler in the afternoon.\n\nTrees are not decoration. In a hot climate they are infrastructure — free air conditioning that also feeds us.",
                'likes' => 89,
            ],
            [
                'author_name' => 'Yuki Tanaka',
                'country' => 'Japan',
                'category' => 'experience',
                'title' => 'Forest bathing healed more than my stress',
                'body' => "After a difficult year I started spending one quiet hour a week simply sitting in the woods — no phone, no goal. The Japanese call it shinrin-yoku, forest bathing.\n\nMy doctor noticed my blood pressure dropped. But more than that, I rediscovered a kind of calm I thought I had lost forever.",
                'likes' => 134,
            ],
            [
                'author_name' => 'Carlos Mendoza',
                'country' => 'Mexico',
                'category' => 'idea',
                'title' => 'A community seed library changed our neighbourhood',
                'body' => "We started a 'seed library' at the local community centre — anyone can take seeds for free and bring back seeds from their harvest.\n\nIn one year we shared dozens of native plant species and met neighbours we had lived beside for decades. Seeds turned out to be a wonderful excuse to build a community.",
                'likes' => 71,
            ],
            [
                'author_name' => 'Fatima Zahra',
                'country' => 'Morocco',
                'category' => 'experience',
                'title' => 'Bringing back the argan trees of my childhood',
                'body' => "The argan forests near our home had been shrinking for years. A women\'s cooperative I joined now protects and replants them, and sells the oil to support our families.\n\nWe are not just saving trees — we are saving a way of life, and giving our daughters a future rooted in this land.",
                'likes' => 156,
            ],
            [
                'author_name' => 'Oliver Smith',
                'country' => 'Australia',
                'category' => 'finding',
                'title' => 'After the bushfires, the forest surprised us all',
                'body' => "Months after the fires swept through, I returned expecting nothing but ash. Instead, bright green shoots were bursting straight out of the blackened eucalyptus trunks.\n\nSome of these trees evolved to survive fire. Nature\'s resilience, when we give it a chance, is staggering.",
                'likes' => 203,
            ],
        ];

        $now = now();

        foreach (array_values($voices) as $i => $data) {
            $createdAt = (clone $now)->subDays(count($voices) - $i)->subHours($i);

            $voice = Voice::create([
                'slug' => Str::slug(Str::limit($data['title'], 60, '')) . '-' . ($i + 1),
                'author_name' => $data['author_name'],
                'country' => $data['country'],
                'category' => $data['category'],
                'title' => $data['title'],
                'body' => $data['body'],
                'likes_count' => $data['likes'],
                'views_count' => $data['likes'] * 7 + 30,
                'status' => 'approved',
                'is_featured' => $i < 2,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $this->seedComments($voice, $createdAt);
        }
    }

    protected function seedComments(Voice $voice, $createdAt): void
    {
        $pool = [
            ['Hannah Lee', 'This is so inspiring — thank you for sharing your story with us. 🌱'],
            ['Tomás Silva', 'We are trying something similar in our town. Sending strength from afar!'],
            ['Nadia Karimi', 'Beautiful. Nature really does give back when we care for it.'],
            ['James Okoro', 'I had never thought about it this way. You have changed how I see my own street.'],
            ['Mei Lin', 'Hope is the best thing we can plant. Wonderful post. 💚'],
        ];

        // Deterministically pick a few comments per voice.
        $count = ($voice->id % 3) + 1;
        for ($c = 0; $c < $count; $c++) {
            [$name, $body] = $pool[($voice->id + $c) % count($pool)];
            $at = (clone $createdAt)->addHours($c + 1);
            VoiceComment::create([
                'voice_id' => $voice->id,
                'author_name' => $name,
                'body' => $body,
                'status' => 'approved',
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }

        $voice->update(['comments_count' => $count]);
    }
}
