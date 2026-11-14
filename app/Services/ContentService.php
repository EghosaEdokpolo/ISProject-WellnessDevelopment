<?php

declare(strict_types=1);

namespace App\Services;

class ContentService
{
    public function getAllArticles(): array
    {
        return [
            'desk-stretch' => [
                'title' => '5-min desk stretch guide',
                'category' => 'Physical',
                'emoji' => '🧘',
                'color' => 'bg-wl-lavender text-wl-indigo',
                'desc' => 'Quick stretches to relieve tension and improve focus at your desk.',
                'featured' => true,
                'content' => '<p class="mb-4">Sitting at a desk for hours can cause stiffness, poor posture, and fatigue. Taking just 5 minutes to stretch can reset your body and improve focus.</p><h3 class="text-lg font-bold mt-6 mb-2">Try these 3 stretches:</h3><ul class="list-disc pl-5 space-y-2"><li><strong>Neck rolls:</strong> Gently drop your chin to your chest and roll your head in a slow circle.</li><li><strong>Shoulder shrugs:</strong> Lift your shoulders to your ears, hold for 3 seconds, and release.</li><li><strong>Seated spinal twist:</strong> Sit up straight, place your right hand on your left knee, and twist gently to look over your left shoulder.</li></ul>'
            ],
            'exam-stress' => [
                'title' => 'Managing exam-season stress',
                'category' => 'Mental',
                'emoji' => '🧠',
                'color' => 'bg-wl-mint text-emerald-700',
                'desc' => 'Practical coping strategies for the most stressful time of the year.',
                'featured' => false,
                'content' => '<p class="mb-4">Exam season can be an incredibly stressful time for both students and staff. It is normal to feel overwhelmed, but managing your stress is key to maintaining your wellbeing.</p><h3 class="text-lg font-bold mt-6 mb-2">Coping Strategies:</h3><ul class="list-disc pl-5 space-y-2"><li><strong>Box Breathing:</strong> Inhale for 4 seconds, hold for 4, exhale for 4, hold for 4. Repeat 5 times.</li><li><strong>Take Micro-breaks:</strong> Step away from your desk every 45 minutes to look at something far away and stretch.</li><li><strong>Stay Hydrated:</strong> Dehydration is linked to increased anxiety and fatigue.</li></ul>'
            ],
            'nutrition-budget' => [
                'title' => 'Nutrition on a campus budget',
                'category' => 'Physical',
                'emoji' => '🥗',
                'color' => 'bg-wl-lavender text-wl-indigo',
                'desc' => 'How to eat healthy without draining your wallet.',
                'featured' => false,
                'content' => '<p class="mb-4">Eating healthy doesn’t have to drain your wallet. By focusing on whole foods and local produce, you can maintain a balanced diet affordably.</p><h3 class="text-lg font-bold mt-6 mb-2">Budget-Friendly Tips:</h3><ul class="list-disc pl-5 space-y-2"><li>Buy seasonal fruits and vegetables from local markets.</li><li>Prep meals in batches on Sunday to avoid buying lunch every day.</li><li>Eggs, beans, and lentils are incredibly cheap and high in protein.</li></ul>'
            ],
            'sleep-shift' => [
                'title' => 'Sleep hygiene for shift workers',
                'category' => 'Mental',
                'emoji' => '🌙',
                'color' => 'bg-wl-mint text-emerald-700',
                'desc' => 'Optimize your sleep environment when working irregular hours.',
                'featured' => false,
                'content' => '<p class="mb-4">For staff who work late shifts, getting quality sleep can be difficult. Sleep is vital for both physical health and emotional regulation.</p><h3 class="text-lg font-bold mt-6 mb-2">Improving Your Sleep:</h3><ul class="list-disc pl-5 space-y-2"><li><strong>Blackout curtains:</strong> Block out daytime light if you are sleeping during the day.</li><li><strong>Cool environment:</strong> Keep your bedroom cool and well-ventilated.</li><li><strong>Wind-down routine:</strong> Avoid screens and caffeine for at least an hour before you intend to sleep.</li></ul>'
            ],
            'peer-support' => [
                'title' => 'Peer support: when to seek help',
                'category' => 'Social',
                'emoji' => '🤝',
                'color' => 'bg-wl-lilac text-purple-700',
                'desc' => 'Recognizing the signs that you or a colleague need professional support.',
                'featured' => false,
                'content' => '<p class="mb-4">We all have bad days, but sometimes the weight doesn’t go away on its own. Knowing when to lean on a colleague or seek professional help is a sign of strength.</p><h3 class="text-lg font-bold mt-6 mb-2">Signs you should reach out:</h3><ul class="list-disc pl-5 space-y-2"><li>You feel persistently sad or hopeless for more than two weeks.</li><li>You are withdrawing from friends, family, or hobbies you used to enjoy.</li><li>Your appetite or sleep patterns have changed drastically.</li><li>If you or someone you know feels this way, contact the Strathmore Counselling Centre.</li></ul>'
            ],
            'financial-wellbeing' => [
                'title' => 'Financial wellbeing basics',
                'category' => 'Financial',
                'emoji' => '💰',
                'color' => 'bg-wl-cream text-amber-700 border border-amber-200',
                'desc' => 'The 50/30/20 rule and how to start tracking your expenses.',
                'featured' => false,
                'content' => '<p class="mb-4">Financial stress is one of the leading causes of anxiety. Taking control of your finances is a huge step towards overall wellness.</p><h3 class="text-lg font-bold mt-6 mb-2">The 50/30/20 Rule:</h3><ul class="list-disc pl-5 space-y-2"><li><strong>50% Needs:</strong> Rent, groceries, utilities.</li><li><strong>30% Wants:</strong> Dining out, entertainment, hobbies.</li><li><strong>20% Savings:</strong> Emergency fund and investments.</li></ul><p class="mt-4">Start by tracking your expenses for one month to see where your money is actually going.</p>'
            ],
        ];
    }

    public function calculateReadTime(string $content): int
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, ceil($wordCount / 200));
    }
}