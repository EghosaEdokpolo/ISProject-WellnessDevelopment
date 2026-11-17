<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CheckIn;
use App\Models\Content;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\PointsTransaction;
use App\Models\Registration;
use App\Models\User;
use App\Models\Waiver;
use App\Models\WellbeingResponse;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean up ALL old data safely to reset IDs back to 1
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('content_user')->truncate();
        Content::truncate();
        PointsTransaction::truncate();
        WellbeingResponse::truncate();
        Feedback::truncate();
        CheckIn::truncate();
        Waiver::truncate();
        Registration::truncate();
        Event::truncate();
        User::truncate(); // Resets auto-increment to 1
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Create the HR Coordinator (ID = 1, Staff ID = 000001)
        $hr = User::create([
            'name'           => 'Grace Wanjiru',
            'staff_id'       => '000001',
            'email'          => 'hr.admin@strathmore.edu',
            'password'       => Hash::make('password'),
            'role'           => 'hr_coordinator',
            'department'     => 'Human Resources',
            'points_balance' => 0,
        ]);

        // 3. Curated Lists of Authentic Kenyan Names & All 8 Strathmore Departments
        $firstNames = [
            'Brian', 'Kevin', 'Dennis', 'Peter', 'John', 'James', 'David', 'Samuel', 'Joseph', 'Daniel',
            'Michael', 'Charles', 'Thomas', 'George', 'Paul', 'Eric', 'Collins', 'Victor', 'Anthony', 'Vincent',
            'Faith', 'Mercy', 'Esther', 'Naomi', 'Ruth', 'Sarah', 'Rebecca', 'Rachel', 'Leah',
            'Hannah', 'Deborah', 'Mary', 'Martha', 'Elizabeth', 'Catherine', 'Anne', 'Jane', 'Lucy', 'Rose',
            'Achieng', 'Atieno', 'Chebet', 'Wambui', 'Njeri', 'Muthoni', 'Nyambura', 'Akinyi', 'Adhiambo', 'Kerubo',
        ];

        $lastNames = [
            'Otieno', 'Omondi', 'Ochieng', 'Onyango', 'Ouma', 'Ogutu', 'Okoth', 'Obonyo', 'Odhiambo', 'Omollo',
            'Mutiso', 'Kimani', 'Kariuki', 'Mwangi', 'Kamau', 'Njoroge', 'Wanjiru', 'Wairimu', 'Wambui', 'Chebet',
            'Kiptoo', 'Korir', 'Langat', 'Rotich', 'Sang', 'Cheruiyot', 'Wafula', 'Wanjala', 'Barasa', 'Simiyu',
            'Wekesa', 'Wamalwa', 'Muthama', 'Mutua', 'Musyoka', 'Kilonzo', 'Ngugi', 'Ndungu', 'Gitau', 'Maina',
        ];

        $departments = [
            'School of Computing and Engineering Sciences',
            'Strathmore Business School',
            'School of Law',
            'School of Humanities and Social Sciences',
            'Finance and Accounting',
            'Human Resources',
            'Cafeteria Services',
            'Housekeeping & Maintenance',
        ];

        // 4. Generate 50 Staff Members (IDs 2-51, Staff IDs 000002-000051)
        $staff = collect();

        for ($i = 0; $i < 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName  = $lastNames[array_rand($lastNames)];
            $fullName  = "{$firstName} {$lastName}";
            $email     = strtolower($firstName . '.' . $lastName) . ($i + 10) . '@strathmore.edu';

            $user = User::create([
                'name'           => $fullName,
                'staff_id'       => 'temp-' . Str::random(5),
                'email'          => $email,
                'password'       => Hash::make('Strathmore2026!'),
                'role'           => 'staff',
                'department'     => $departments[$i % count($departments)],
                'points_balance' => 0,
            ]);

            $user->staff_id = str_pad((string) $user->id, 6, '0', STR_PAD_LEFT);
            $user->save();

            $staff->push($user);
        }

        // 5. Create 8 Distinct Past Events (1 per Wellness Pillar)
        $pastCatalog = [
            ['name' => 'Staff Yoga & Mobility Session',        'category' => 'physical',      'days_ago' => 34, 'location' => 'University Sports Complex'],
            ['name' => 'Mental Health & Resilience Clinic',    'category' => 'mental',        'days_ago' => 28, 'location' => 'Main Campus - Auditorium'],
            ['name' => 'SACCO & Financial Literacy Workshop',  'category' => 'financial',     'days_ago' => 23, 'location' => 'SBS Executive Auditorium'],
            ['name' => 'Inter-Faculty Team Building Mixer',    'category' => 'social',        'days_ago' => 19, 'location' => 'Student Centre Amphitheatre'],
            ['name' => 'Ergonomic Workstation & Hybrid Lab',   'category' => 'occupational',  'days_ago' => 15, 'location' => 'Phase II Seminar Room 3'],
            ['name' => 'AI & Lifelong Learning Forum',         'category' => 'intellectual',  'days_ago' => 11, 'location' => 'Main Library Innovation Hub'],
            ['name' => 'Purposeful Leadership Reflection',     'category' => 'spiritual',     'days_ago' => 7,  'location' => 'Chapel Conference Hall'],
            ['name' => 'Green Campus Sustainability Walk',     'category' => 'environmental', 'days_ago' => 4,  'location' => 'Botanical Courtyard'],
        ];

        $feedbackTexts = [
            'Really enjoyed the session, very refreshing.',
            'Great initiative. Would love more of these.',
            'The facilitator was excellent.',
            'Useful content, well organised.',
            'Nice to connect with colleagues outside work.',
            null,
        ];

        foreach ($pastCatalog as $spec) {
            $event = Event::create([
                'name'            => $spec['name'],
                'category'        => $spec['category'],
                'description'     => 'A wellness initiative organized by Strathmore University People & Culture.',
                'starts_at'       => Carbon::now()->subDays($spec['days_ago'])->setTime(10, 0),
                'location'        => $spec['location'],
                'capacity'        => 80,
                'created_by'      => $hr->id,
                'points_checkin'  => 20,
                'points_feedback' => 10,
            ]);

            $registeredStaff = $staff->random(rand(24, 34));

            foreach ($registeredStaff as $person) {
                $regCreatedAt = $event->starts_at->copy()->subDays(rand(1, 5));

                $reg = Registration::create([
                    'user_id'       => $person->id,
                    'event_id'      => $event->id,
                    'status'        => 'registered',
                    'consent_given' => true,
                    'created_at'    => $regCreatedAt,
                    'updated_at'    => $regCreatedAt,
                ]);

                // 90% sign waiver
                if (rand(1, 100) <= 90) {
                    Waiver::create([
                        'registration_id' => $reg->id,
                        'signature_name'  => $person->name,
                        'signed_at'       => $regCreatedAt->copy()->addMinutes(5),
                    ]);
                    $this->awardPoints($person, $reg, 'waiver_signed', 5);
                }

                // 80% check in
                if (rand(1, 100) <= 80) {
                    $checkedInAt = $event->starts_at->copy()->addMinutes(rand(0, 25));

                    CheckIn::create([
                        'registration_id' => $reg->id,
                        'checked_in_at'   => $checkedInAt,
                        'method'          => 'qr',
                    ]);
                    $reg->update(['status' => 'checked_in', 'updated_at' => $checkedInAt]);
                    $this->awardPoints($person, $reg, 'attended_event', (int) $event->points_checkin);

                    // 75% complete WHO-5 pre & post surveys within 24h
                    if (rand(1, 100) <= 75) {
                        $preItems    = [rand(1, 3), rand(1, 3), rand(1, 3), rand(1, 3), rand(1, 3)];
                        $postItems   = [rand(3, 5), rand(3, 5), rand(3, 5), rand(3, 5), rand(3, 5)];
                        $submittedAt = $event->starts_at->copy()->addHours(rand(2, 18));

                        WellbeingResponse::create([
                            'registration_id' => $reg->id,
                            'stage'           => 'pre',
                            'item_cheerful'   => $preItems[0],
                            'item_calm'       => $preItems[1],
                            'item_active'     => $preItems[2],
                            'item_rested'     => $preItems[3],
                            'item_interested' => $preItems[4],
                            'total_score'     => array_sum($preItems),
                            'created_at'      => $regCreatedAt,
                            'updated_at'      => $regCreatedAt,
                        ]);

                        WellbeingResponse::create([
                            'registration_id' => $reg->id,
                            'stage'           => 'post',
                            'item_cheerful'   => $postItems[0],
                            'item_calm'       => $postItems[1],
                            'item_active'     => $postItems[2],
                            'item_rested'     => $postItems[3],
                            'item_interested' => $postItems[4],
                            'total_score'     => array_sum($postItems),
                            'created_at'      => $submittedAt,
                            'updated_at'      => $submittedAt,
                        ]);

                        // 85% leave feedback
                        if (rand(1, 100) <= 85) {
                            Feedback::create([
                                'registration_id'    => $reg->id,
                                'satisfaction_stars' => rand(4, 5),
                                'social_quality'     => rand(7, 10),
                                'comments'           => $feedbackTexts[array_rand($feedbackTexts)],
                                'created_at'         => $submittedAt,
                                'updated_at'         => $submittedAt,
                            ]);
                            $reg->update(['status' => 'completed', 'updated_at' => $submittedAt]);
                            $this->awardPoints($person, $reg, 'feedback_submitted', (int) $event->points_feedback);
                        }
                    }
                }
            }
        }

        // 6. Create 3 Upcoming Events so Check-In Views (scan & index) are ready for live demo
        $upcomingCatalog = [
            ['name' => 'Madaraka Campus Step Challenge Kickoff', 'category' => 'physical',  'days_ahead' => 2,  'location' => 'University Sports Complex'],
            ['name' => 'Stress Management & Mindfulness Lab',    'category' => 'mental',    'days_ahead' => 6,  'location' => 'Main Campus - Auditorium'],
            ['name' => 'Staff Retirement & SACCO Clinic',        'category' => 'financial', 'days_ahead' => 12, 'location' => 'SBS Executive Lounge'],
        ];

        foreach ($upcomingCatalog as $spec) {
            $upcomingEvent = Event::create([
                'name'            => $spec['name'],
                'category'        => $spec['category'],
                'description'     => 'Upcoming Strathmore University staff wellness session.',
                'starts_at'       => Carbon::now()->addDays($spec['days_ahead'])->setTime(9, 0),
                'location'        => $spec['location'],
                'capacity'        => 80,
                'created_by'      => $hr->id,
                'points_checkin'  => 25,
                'points_feedback' => 10,
            ]);

            foreach ($staff->take(15) as $person) {
                $reg = Registration::create([
                    'user_id'       => $person->id,
                    'event_id'      => $upcomingEvent->id,
                    'status'        => 'registered',
                    'consent_given' => true,
                ]);

                Waiver::create([
                    'registration_id' => $reg->id,
                    'signature_name'  => $person->name,
                    'signed_at'       => now(),
                ]);
                $this->awardPoints($person, $reg, 'waiver_signed', 5);
            }
        }

        // 7. Create 8 Wellness Content Hub Articles (1 per Wellness Dimension)
        $articles = [
            [
                'title'    => 'Desk Mobility: 5-Minute Stretches Between Lectures and Meetings',
                'category' => 'physical',
                'excerpt'  => 'Simple movement resets you can do right at your workstation to relieve neck, shoulder, and lower-back stiffness.',
                'body'     => "Prolonged sitting during back-to-back lectures, grading, or administrative reviews places steady strain on the cervical spine and hip flexors.\n\nTry incorporating a five-minute movement break every 90 minutes:\n1. Seated Thoracic Twists — 5 slow rotations per side.\n2. Chest & Shoulder Opener — clasp hands behind your chair and lift gently for 20 seconds.\n3. Standing Calf Raises & Ankle Circles — boost circulation before your next session.\n\nConsistent micro-movements improve afternoon alertness and reduce musculoskeletal fatigue across the semester.",
            ],
            [
                'title'    => 'Recognizing Academic & Administrative Burnout Early',
                'category' => 'mental',
                'excerpt'  => 'Practical signs of cognitive overload and evidence-based recovery habits for university faculty and staff.',
                'body'     => "High-performing university environments demand sustained focus during exam periods, admissions cycles, and budget reviews. When recovery lags behind workload, emotional exhaustion follows.\n\nKey early warning signs include persistent difficulty unwinding after hours, irritability, and declining concentration.\n\nProtect your mental bandwidth by scheduling a 15-minute transition ritual at the end of your workday, batching email checks, and utilizing Strathmore's confidential staff counselling resources whenever needed.",
            ],
            [
                'title'    => 'Maximizing Your Staff SACCO & Pension Contributions',
                'category' => 'financial',
                'excerpt'  => 'A step-by-step guide to automating monthly savings, building an emergency buffer, and planning long-term wealth.',
                'body'     => "Financial peace of mind directly supports overall wellbeing. Small, automated adjustments to your monthly payroll deductions compound significantly over time.\n\nStart by building a three-to-six-month emergency fund in a liquid money market or SACCO deposit account before locking funds into long-term investments. Review your pension beneficiary details annually and attend People & Culture financial clinics for personalized retirement projections.",
            ],
            [
                'title'    => 'Building Cross-Departmental Camaraderie at Madaraka Campus',
                'category' => 'social',
                'excerpt'  => 'Why informal peer connections across schools and support units strengthen workplace morale and collaboration.',
                'body'     => "Strong social ties at work are one of the highest predictors of job satisfaction and resilience. Yet busy schedules often keep faculty and administrative teams siloed within their own buildings.\n\nJoining an inter-faculty wellness mixer, walking group, or lunchtime coffee table creates natural opportunities to connect with colleagues across SCES, SBS, Law, Finance, and Operations.",
            ],
            [
                'title'    => 'Deep Work & AI Workflows for Academic and Admin Teams',
                'category' => 'intellectual',
                'excerpt'  => 'Structuring distraction-free blocks for research, curriculum design, and strategic reporting.',
                'body'     => "Constant notifications fragment attention and double the time required for complex analytical tasks. Dedicating the first 90 minutes of your morning to deep, uninterrupted work—paired with responsible AI summarization tools for routine drafting—frees up hours each week for high-impact teaching, research, and student mentorship.",
            ],
            [
                'title'    => 'Ergonomic Workstation Checklist for Hybrid & Office Work',
                'category' => 'occupational',
                'excerpt'  => 'Align your monitor height, chair lumbar support, and lighting to prevent eye strain and wrist fatigue.',
                'body'     => "A well-calibrated workspace prevents chronic strain:\n- Position the top third of your monitor at eye level, roughly an arm's length away.\n- Keep elbows at a 90-degree angle with wrists neutral while typing.\n- Ensure both feet rest flat on the floor or on a supportive footrest.\n\nReach out to People & Culture if you need an ergonomic desk assessment.",
            ],
            [
                'title'    => 'Green Habits: Reducing Paper & Energy Footprints on Campus',
                'category' => 'environmental',
                'excerpt'  => 'Everyday actions that keep Strathmore University’s Madaraka campus clean, green, and energy-efficient.',
                'body'     => "Our physical surroundings shape how calm and energized we feel throughout the day. Simple habits—such as defaulting to digital approvals, powering down office monitors overnight, and using refillable water bottles—reduce waste and contribute to Strathmore's sustainability stewardship goals.",
            ],
            [
                'title'    => 'Purposeful Service & Daily Gratitude in University Life',
                'category' => 'spiritual',
                'excerpt'  => 'Grounding daily responsibilities in meaning, ethical leadership, and reflection.',
                'body'     => "Taking five quiet minutes at the start or close of the workday to reflect on the human impact of our roles—whether lecturing, advising students, maintaining facilities, or managing finances—transforms routine tasks into purposeful service.",
            ],
        ];

        $createdArticles = collect();
        foreach ($articles as $idx => $article) {
            $createdArticles->push(Content::create([
                'slug'         => Str::slug($article['title']),
                'title'        => $article['title'],
                'category'     => $article['category'],
                'excerpt'      => $article['excerpt'],
                'body'         => $article['body'],
                'image_url'    => null,
                'is_published' => true,
                'created_at'   => Carbon::now()->subDays(20 - ($idx * 2)),
            ]));
        }

        // Bookmark 2 articles for Staff ID 000002 so "Saved" state is visible immediately
        if ($staff->isNotEmpty()) {
            $staff->first()->savedContents()->sync($createdArticles->take(2)->pluck('id'));
        }

        Cache::flush();

        $this->command->info('Success! Created HR Admin (000001), 50 Staff (000002-000051), 8 Past Events, 3 Upcoming Events, and 8 Wellness Articles.');
    }

    private function awardPoints(User $user, Registration $registration, string $reason, int $points): void
    {
        PointsTransaction::create([
            'user_id'         => $user->id,
            'registration_id' => $registration->id,
            'reason'          => $reason,
            'points'          => $points,
        ]);

        $user->increment('points_balance', $points);
    }
}