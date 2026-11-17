<?php

namespace Database\Seeders;

use App\Models\Content;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'category' => 'physical',
                'title' => 'The Science of Micro-Movement at Your Desk',
                'excerpt' => 'Short, frequent movement breaks outperform long workouts for sedentary workers.',
                'body' => "It is no secret that working at a university often involves long hours at a desk—grading papers, writing research, or managing administrative tasks. But prolonged sitting can lead to stiff joints, poor circulation, and fatigue.\n\nThe good news? You don't need a 90-minute gym session to counteract it. The science of 'micro-movements' shows that short, frequent bursts of activity throughout the day are incredibly effective.\n\nTry this: Set a timer for 50 minutes. When it goes off, stand up, stretch your arms overhead, and take a 2-minute walk (even if it's just to the water dispenser and back). Do a few gentle neck rolls and shoulder shrugs. These micro-breaks reset your posture, boost blood flow to the brain, and actually improve your focus for the next hour."
            ],
            [
                'category' => 'mental',
                'title' => 'Five-Minute Mindfulness for Busy Professionals',
                'excerpt' => 'How to use the 5-4-3-2-1 grounding technique between meetings.',
                'body' => "Academic environments can be highly demanding. Between student queries, deadlines, and the pressure of results, it is easy to feel overwhelmed. When anxiety spikes, your nervous system enters 'fight or flight' mode.\n\nMindfulness doesn't require an hour of meditation. You can ground yourself in just five minutes using the 5-4-3-2-1 technique. Right at your desk, silently acknowledge:\n\n• 5 things you can see (a pen, your monitor, the window)\n• 4 things you can feel (the chair beneath you, your feet on the floor)\n• 3 things you can hear (the hum of the AC, distant footsteps)\n• 2 things you can smell (coffee, paper)\n• 1 thing you can taste (take a sip of water or tea)\n\nThis simple cognitive exercise forces your brain to focus on the present moment, lowering cortisol levels and allowing you to return to your tasks with a clear head."
            ],
            [
                'category' => 'financial',
                'title' => 'Building an Emergency Fund on a Fixed Salary',
                'excerpt' => 'A practical, step-by-step budget framework for university staff.',
                'body' => "Financial stress is one of the leading causes of anxiety, but it is highly manageable with a solid plan. Building an emergency fund on a fixed salary comes down to consistency, not massive windfalls.\n\nFirst, aim for a starter buffer of one month's essential expenses, eventually building up to three to six months. The trick is to automate it. On payday, set up a standing order to transfer a small, manageable amount (even 5% of your income) to a separate savings account that you don't touch.\n\nSecond, audit your recurring subscriptions. Are you paying for multiple streaming services? Can you renegotiate your internet plan? Redirect those small savings directly into your emergency fund. Pay yourself first, and the peace of mind it buys is priceless."
            ],
            [
                'category' => 'social',
                'title' => 'Combating Workplace Isolation',
                'excerpt' => 'Three conversations that build real connection with colleagues.',
                'body' => "In academic and administrative roles, it is easy to become isolated in your own office or department, leading to feelings of loneliness. Building a social support system at work is vital for your mental well-being.\n\nMake a conscious effort to connect beyond work tasks. Try initiating three simple conversations this week:\n1. A genuine 'How are you?' (and actually listen).\n2. A compliment on a colleague's work or presentation.\n3. A casual invite: 'I'm going for a coffee run, would you like anything?'\n\nYou can also join or form a staff walking group during lunch breaks. Shared, non-work-related interactions build strong social bonds that make the workplace feel like a community rather than just a building."
            ],
            [
                'category' => 'intellectual',
                'title' => 'Continuous Learning Without Burning Out',
                'excerpt' => 'How to learn a new skill in 20 minutes a day.',
                'body' => "As professionals, we are constantly told to 'upskill' and 'keep learning.' But when you spend all day using your brain for work, adding another online course can lead to intellectual burnout.\n\nThe key is separating 'work learning' from 'play learning.' Dedicate just 20 minutes a day to learning something purely for the joy of it. Read a fiction novel, watch a documentary on a random historical event, or start a beginner course in a completely unrelated field—like photography, coding, or a new language.\n\nBy shifting your intellectual focus to something that is purely for your own enjoyment, you stimulate creativity without draining the mental energy you need for your actual job."
            ],
            [
                'category' => 'occupational',
                'title' => 'Setting Boundaries That Stick',
                'excerpt' => 'Scripts and habits for protecting your focus time.',
                'body' => "With smartphones and university email apps, work has a way of bleeding into our evenings and weekends. If you don't set boundaries, you will eventually burn out.\n\nStart by communicating your availability clearly. Set an automated out-of-office reply for the evenings. If a colleague constantly interrupts you for non-urgent matters, try saying, 'I'm currently finishing a report, but I can come by your office at 2 PM to discuss this.'\n\nBlock out 'Focus Time' on your calendar just like you would a meeting. During this time, close your email client and put your phone in a drawer. Remember: setting a boundary isn't rude; it's a professional way of ensuring you are actually productive."
            ],
            [
                'category' => 'environmental',
                'title' => 'Greening Your Workspace',
                'excerpt' => 'Simple changes that improve air quality, light, and mood.',
                'body' => "You spend roughly 40 hours a week in your office or workspace. A sterile, cluttered environment can subconsciously drain your energy.\n\nTransform your space into a restorative environment. Adding a couple of resilient desk plants, like a snake plant or pothos, improves air quality and introduces a calming element of nature. \n\nMaximize natural light if possible. If your desk is far from a window, ensure your task lighting is warm and eye-friendly to reduce glare. Finally, take five minutes at the end of each workday to declutter your desk. Coming into a clean, organized space tomorrow morning reduces morning anxiety and sets a positive tone for the day."
            ],
            [
                'category' => 'spiritual',
                'title' => 'Finding Meaning in Everyday Work',
                'excerpt' => 'Reflection exercises grounded in purpose, not productivity.',
                'body' => "It is easy to get lost in the daily grind of tasks, meetings, and emails, forgetting why we do what we do. Spiritual wellness at work is about connecting your daily tasks to a larger sense of purpose.\n\nTake a moment at the end of your week to reflect on one positive impact you had. Did you help a struggling student? Did you ensure a critical administrative process ran smoothly so your team could succeed? Write it down.\n\nRecognizing how your individual role contributes to the broader mission of the university—whether that is education, research, or community support—fosters a profound sense of meaning. When you remember your 'why,' daily challenges become much easier to navigate."
            ],
        ];

        foreach ($items as $item) {
            Content::updateOrCreate(
                ['slug' => Str::slug($item['title'])],
                [
                    'title'        => $item['title'],
                    'category'     => $item['category'],
                    'excerpt'      => $item['excerpt'],
                    'body'         => $item['body'],
                    'image_url'    => null,
                    'is_published' => true,
                ]
            );
        }
    }
}