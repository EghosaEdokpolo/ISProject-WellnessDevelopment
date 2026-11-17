<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CheckIn;
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

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the Strathmore University WPMIS database with complete, synchronized demo records.
     */
    public function run(): void
    {
        // Ensure MySQL columns accept all 8 wellness pillars and all check-in methods ('qr_scan', 'manual', 'hr_retroactive')
        try {
            DB::statement("ALTER TABLE `check_ins` MODIFY COLUMN `method` VARCHAR(50) NOT NULL DEFAULT 'manual'");
            DB::statement("ALTER TABLE `events` MODIFY COLUMN `category` ENUM('physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual') NOT NULL");
        } catch (\Throwable $e) {
            // Safely continue if running on SQLite or already altered
        }

        // ------------------------------------------------------------------
        // STEP 1: DEMO ACCOUNTS (HR COORDINATOR + PRIMARY STAFF DEMO USER)
        // ------------------------------------------------------------------
        $hrAdmin = User::updateOrCreate(
            ['staff_id' => 100010],
            [
                'name'           => 'HR Wellness Coordinator',
                'email'          => 'hr.wellness@strathmore.edu',
                'role'           => 'hr_coordinator',
                'department'     => 'Human Resources',
                'password'       => Hash::make('password'),
                'points_balance' => 0,
            ]
        );

        $demoStaff = User::firstOrCreate(
            ['staff_id' => 100020],
            [
                'name'           => 'Faith Wanjiku',
                'email'          => 'fwanjiku@strathmore.edu',
                'role'           => 'staff',
                'department'     => 'Strathmore Business School',
                'password'       => Hash::make('password'),
                'points_balance' => 0,
            ]
        );

        // ------------------------------------------------------------------
        // STEP 2: 32 STAFF ACCOUNTS ACROSS ALL 8 STRATHMORE DEPARTMENTS
        // Matches DepartmentalEngagementService::shortCode() (SCES, SOL, SBS, SHSS, FIN, HR, CAF, HK)
        // ------------------------------------------------------------------
        $kenyanStaffDirectory = [
            // School of Computing & Engineering Sciences (SCES)
            ['staff_id' => 20101, 'name' => 'Grace Achieng',        'dept' => 'School of Computing & Engineering Sciences'],
            ['staff_id' => 20102, 'name' => 'Kevin Kiprop',         'dept' => 'School of Computing & Engineering Sciences'],
            ['staff_id' => 20103, 'name' => 'Collins Kiptoo',       'dept' => 'School of Computing & Engineering Sciences'],
            ['staff_id' => 20104, 'name' => 'Victor Korir',         'dept' => 'School of Computing & Engineering Sciences'],
            ['staff_id' => 20105, 'name' => 'Eric Rotich',          'dept' => 'School of Computing & Engineering Sciences'],

            // Strathmore Law School (SOL)
            ['staff_id' => 20106, 'name' => 'Mercy Chepkoech',      'dept' => 'Strathmore Law School'],
            ['staff_id' => 20107, 'name' => 'Cynthia Moraa',        'dept' => 'Strathmore Law School'],
            ['staff_id' => 20108, 'name' => 'Hannah Nyambura',      'dept' => 'Strathmore Law School'],
            ['staff_id' => 20109, 'name' => 'Martin Oduor',         'dept' => 'Strathmore Law School'],

            // Strathmore Business School (SBS)
            ['staff_id' => 20110, 'name' => 'Dr. Kamau Njoroge',    'dept' => 'Strathmore Business School'],
            ['staff_id' => 20111, 'name' => 'Peter Kimani',         'dept' => 'Strathmore Business School'],
            ['staff_id' => 20112, 'name' => 'Prof. Miriam Onyango', 'dept' => 'Strathmore Business School'],
            ['staff_id' => 20113, 'name' => 'Irene Muthoni',        'dept' => 'Strathmore Business School'],

            // School of Humanities & Social Sciences (SHSS)
            ['staff_id' => 20114, 'name' => 'Njeri Gathoni',        'dept' => 'School of Humanities & Social Sciences'],
            ['staff_id' => 20115, 'name' => 'Samuel Otieno',        'dept' => 'School of Humanities & Social Sciences'],
            ['staff_id' => 20116, 'name' => 'Esther Mwikali',       'dept' => 'School of Humanities & Social Sciences'],
            ['staff_id' => 20117, 'name' => 'Sharon Atieno',        'dept' => 'School of Humanities & Social Sciences'],

            // Finance Department (FIN)
            ['staff_id' => 20118, 'name' => 'David Mwangi',         'dept' => 'Finance Department'],
            ['staff_id' => 20119, 'name' => 'Beatrice Wambui',      'dept' => 'Finance Department'],
            ['staff_id' => 20120, 'name' => 'Joseph Karanja',       'dept' => 'Finance Department'],
            ['staff_id' => 20121, 'name' => 'Diana Waweru',         'dept' => 'Finance Department'],

            // Human Resources (HR)
            ['staff_id' => 20122, 'name' => 'Wanjiru Mutua',        'dept' => 'Human Resources'],
            ['staff_id' => 20123, 'name' => 'George Maina',         'dept' => 'Human Resources'],
            ['staff_id' => 20124, 'name' => 'Catherine Ndungu',     'dept' => 'Human Resources'],

            // Cafeteria Services (CAF)
            ['staff_id' => 20125, 'name' => 'Lilian Nekesa',        'dept' => 'Cafeteria Services'],
            ['staff_id' => 20126, 'name' => 'Patrick Mutiso',       'dept' => 'Cafeteria Services'],
            ['staff_id' => 20127, 'name' => 'Abigail Cherono',      'dept' => 'Cafeteria Services'],
            ['staff_id' => 20128, 'name' => 'Stephen Makau',        'dept' => 'Cafeteria Services'],

            // Housekeeping & Maintenance (HK)
            ['staff_id' => 20129, 'name' => 'Brian Omondi',         'dept' => 'Housekeeping & Maintenance'],
            ['staff_id' => 20130, 'name' => 'Dennis Barasa',        'dept' => 'Housekeeping & Maintenance'],
            ['staff_id' => 20131, 'name' => 'Lucy Chebet',          'dept' => 'Housekeeping & Maintenance'],
            ['staff_id' => 20132, 'name' => 'Francis Kamau',        'dept' => 'Housekeeping & Maintenance'],
        ];

        $staffUsers = [$demoStaff];

        foreach ($kenyanStaffDirectory as $entry) {
            $slug = Str::slug($entry['name'], '.');
            $staffUsers[] = User::firstOrCreate(
                ['staff_id' => $entry['staff_id']],
                [
                    'name'           => $entry['name'],
                    'email'          => "{$slug}@strathmore.edu",
                    'role'           => 'staff',
                    'department'     => $entry['dept'],
                    'password'       => Hash::make('password'),
                    'points_balance' => 0,
                ]
            );
        }

        // ------------------------------------------------------------------
        // STEP 3: 8 PAST HISTORICAL WELLNESS EVENTS (1 PER WELLNESS PILLAR)
        // ------------------------------------------------------------------
        $pastEventCatalog = [
            [
                'name'            => 'Madaraka Campus Morning Yoga & Mobility',
                'category'        => 'physical',
                'days_ago'        => 68,
                'hour'            => 7,
                'location'        => 'University Sports Complex',
                'capacity'        => 60,
                'points_checkin'  => 25,
                'points_feedback' => 15,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-physical-wellness',
                'description'     => 'Guided morning stretching, posture correction, and cardiovascular mobility session for faculty and administrative staff.',
            ],
            [
                'name'            => 'Burnout Prevention & Stress Resilience Clinic',
                'category'        => 'mental',
                'days_ago'        => 54,
                'hour'            => 14,
                'location'        => 'Strathmore Auditorium',
                'capacity'        => 80,
                'points_checkin'  => 30,
                'points_feedback' => 15,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-mental-wellbeing',
                'description'     => 'Interactive workshop led by clinical psychologists focusing on cognitive load management, mindfulness, and work-life boundaries.',
            ],
            [
                'name'            => 'Staff Retirement & SACCO Wealth Planning',
                'category'        => 'financial',
                'days_ago'        => 42,
                'hour'            => 12,
                'location'        => 'SBS Executive Auditorium',
                'capacity'        => 75,
                'points_checkin'  => 25,
                'points_feedback' => 10,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-financial-literacy',
                'description'     => 'Practical financial literacy forum covering pension optimization, SACCO dividends, treasury bonds, and personal budgeting.',
            ],
            [
                'name'            => 'Inter-Faculty Coffee & Cultural Mixer',
                'category'        => 'social',
                'days_ago'        => 33,
                'hour'            => 16,
                'location'        => 'Student Centre Amphitheatre',
                'capacity'        => 100,
                'points_checkin'  => 20,
                'points_feedback' => 10,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-community',
                'description'     => 'Cross-departmental networking social encouraging peer camaraderie across academic schools and support units.',
            ],
            [
                'name'            => 'AI Productivity & Lifelong Learning Forum',
                'category'        => 'intellectual',
                'days_ago'        => 24,
                'hour'            => 10,
                'location'        => 'Main Library Innovation Hub',
                'capacity'        => 50,
                'points_checkin'  => 25,
                'points_feedback' => 15,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-learning-hub',
                'description'     => 'Hands-on session exploring modern research tools, critical thinking habits, and continuous professional upskilling.',
            ],
            [
                'name'            => 'Ergonomic Workstation & Hybrid Balance Lab',
                'category'        => 'occupational',
                'days_ago'        => 16,
                'hour'            => 11,
                'location'        => 'Phase II Seminar Room 3',
                'capacity'        => 45,
                'points_checkin'  => 20,
                'points_feedback' => 10,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-occupational-health',
                'description'     => 'Workplace ergonomics assessment, desk setup optimization, and sustainable productivity routines for university staff.',
            ],
            [
                'name'            => 'Green Campus Tree Planting & Sustainability Walk',
                'category'        => 'environmental',
                'days_ago'        => 9,
                'hour'            => 8,
                'location'        => 'Strathmore Botanical Courtyard',
                'capacity'        => 70,
                'points_checkin'  => 30,
                'points_feedback' => 10,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-green-campus',
                'description'     => 'Environmental stewardship morning restoring indigenous greenery along the Madaraka campus perimeter.',
            ],
            [
                'name'            => 'Mindful Reflection & Purposeful Leadership Retreat',
                'category'        => 'spiritual',
                'days_ago'        => 4,
                'hour'            => 15,
                'location'        => 'Chapel Conference Hall',
                'capacity'        => 60,
                'points_checkin'  => 25,
                'points_feedback' => 15,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-reflection',
                'description'     => 'Guided ethical reflection on personal values, gratitude practices, and service-oriented university leadership.',
            ],
        ];

        $sampleComments = [
            'Very practical and well-organized session. It helped me reset my energy levels for the rest of the semester.',
            'Loved connecting with colleagues from other departments outside our usual office routines.',
            'The facilitator gave actionable tips we can apply immediately at Strathmore. Would love a follow-up session!',
            'Great timing and venue setup at Madaraka campus. My stress levels dropped noticeably after attending.',
            'Informative, engaging, and directly relevant to our day-to-day work-life balance.',
            'Really appreciated the supportive atmosphere and clear takeaways from the People & Culture team.',
        ];

        foreach ($pastEventCatalog as $eventSpec) {
            $eventDate = Carbon::now()->subDays($eventSpec['days_ago'])->setTime($eventSpec['hour'], 0, 0);

            $event = Event::firstOrCreate(
                ['name' => $eventSpec['name']],
                [
                    'category'        => $eventSpec['category'],
                    'starts_at'       => $eventDate,
                    'location'        => $eventSpec['location'],
                    'capacity'        => $eventSpec['capacity'],
                    'points_checkin'  => $eventSpec['points_checkin'],
                    'points_feedback' => $eventSpec['points_feedback'],
                    'comlink'         => $eventSpec['comlink'],
                    'description'     => $eventSpec['description'],
                    'created_by'      => $hrAdmin->id,
                    'created_at'      => (clone $eventDate)->subDays(10),
                ]
            );

            $selectedIndices = array_unique(array_merge(
                $eventSpec['days_ago'] >= 24 ? [0] : [],
                (array) array_rand($staffUsers, rand(14, 19))
            ));

            foreach ($selectedIndices as $idx) {
                $participant = $staffUsers[$idx];
                $regCreatedAt = (clone $eventDate)->subDays(rand(1, 7));

                $registration = Registration::firstOrCreate(
                    [
                        'user_id'  => $participant->id,
                        'event_id' => $event->id,
                    ],
                    [
                        'status'        => 'completed',
                        'consent_given' => true,
                        'created_at'    => $regCreatedAt,
                        'updated_at'    => (clone $eventDate)->addHours(4),
                    ]
                );

                // 1. Mandatory Waiver Signed (+5 pts)
                Waiver::firstOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'signature_name' => $participant->name,
                        'signed_at'      => (clone $regCreatedAt)->addMinutes(2),
                    ]
                );

                $waiverTx = PointsTransaction::firstOrCreate(
                    [
                        'user_id'         => $participant->id,
                        'registration_id' => $registration->id,
                        'reason'          => 'waiver_signed',
                    ],
                    [
                        'points'     => 5,
                        'created_at' => (clone $regCreatedAt)->addMinutes(2),
                    ]
                );
                if ($waiverTx->wasRecentlyCreated) {
                    $participant->increment('points_balance', 5);
                }

                // 2. Verified Check-In (+points_checkin)
                CheckIn::firstOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'checked_in_at' => (clone $eventDate)->addMinutes(rand(0, 15)),
                        'method'        => 'qr_scan',
                    ]
                );

                $checkinPts = (int) $eventSpec['points_checkin'];
                if ($checkinPts > 0) {
                    $checkinTx = PointsTransaction::firstOrCreate(
                        [
                            'user_id'         => $participant->id,
                            'registration_id' => $registration->id,
                            'reason'          => 'attended_event',
                        ],
                        [
                            'points'     => $checkinPts,
                            'created_at' => (clone $eventDate)->addMinutes(10),
                        ]
                    );
                    if ($checkinTx->wasRecentlyCreated) {
                        $participant->increment('points_balance', $checkinPts);
                    }
                }

                // 3. Pre-Event WHO-5 Baseline (6 to 12 / 25)
                $preCheerful   = rand(1, 3);
                $preCalm       = rand(1, 2);
                $preActive     = rand(1, 3);
                $preRested     = rand(1, 2);
                $preInterested = rand(1, 3);

                WellbeingResponse::firstOrCreate(
                    [
                        'registration_id' => $registration->id,
                        'stage'           => 'pre',
                    ],
                    [
                        'item_cheerful'   => $preCheerful,
                        'item_calm'       => $preCalm,
                        'item_active'     => $preActive,
                        'item_rested'     => $preRested,
                        'item_interested' => $preInterested,
                        'total_score'     => $preCheerful + $preCalm + $preActive + $preRested + $preInterested,
                        'created_at'      => $regCreatedAt,
                    ]
                );

                // 4. Post-Event WHO-5 Outcome (17 to 23 / 25, submitted within 24h)
                $postCheerful   = rand(3, 5);
                $postCalm       = rand(3, 5);
                $postActive     = rand(3, 5);
                $postRested     = rand(3, 4);
                $postInterested = rand(4, 5);
                $submittedAt    = (clone $eventDate)->addHours(rand(2, 16));

                WellbeingResponse::firstOrCreate(
                    [
                        'registration_id' => $registration->id,
                        'stage'           => 'post',
                    ],
                    [
                        'item_cheerful'   => $postCheerful,
                        'item_calm'       => $postCalm,
                        'item_active'     => $postActive,
                        'item_rested'     => $postRested,
                        'item_interested' => $postInterested,
                        'total_score'     => $postCheerful + $postCalm + $postActive + $postRested + $postInterested,
                        'created_at'      => $submittedAt,
                    ]
                );

                // 5. Event Feedback (+points_feedback)
                Feedback::firstOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'satisfaction_stars' => rand(4, 5),
                        'social_quality'     => rand(7, 10),
                        'comments'           => $sampleComments[array_rand($sampleComments)],
                        'created_at'         => $submittedAt,
                    ]
                );

                $feedbackPts = (int) $eventSpec['points_feedback'];
                if ($feedbackPts > 0) {
                    $feedbackTx = PointsTransaction::firstOrCreate(
                        [
                            'user_id'         => $participant->id,
                            'registration_id' => $registration->id,
                            'reason'          => 'feedback_submitted',
                        ],
                        [
                            'points'     => $feedbackPts,
                            'created_at' => $submittedAt,
                        ]
                    );
                    if ($feedbackTx->wasRecentlyCreated) {
                        $participant->increment('points_balance', $feedbackPts);
                    }
                }
            }
        }

        // ------------------------------------------------------------------
        // STEP 4: 4 UPCOMING EVENTS WITH PRE-REGISTRATIONS, WAIVERS & BASELINES
        // ------------------------------------------------------------------
        $upcomingEventCatalog = [
            [
                'name'            => 'Annual Staff Fitness & Step Challenge Kickoff',
                'category'        => 'physical',
                'days_ahead'      => 4,
                'hour'            => 9,
                'location'        => 'University Sports Complex',
                'capacity'        => 80,
                'points_checkin'  => 30,
                'points_feedback' => 15,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-step-challenge',
                'description'     => 'Launch of the inter-departmental 10,000 steps daily wellness challenge with health screenings and team signups.',
            ],
            [
                'name'            => 'Emotional Intelligence & Conflict Resolution Workshop',
                'category'        => 'mental',
                'days_ahead'      => 11,
                'hour'            => 14,
                'location'        => 'Strathmore Auditorium',
                'capacity'        => 60,
                'points_checkin'  => 25,
                'points_feedback' => 15,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-eq-workshop',
                'description'     => 'Practical communication frameworks for academic and administrative teams navigating high-pressure semester cycles.',
            ],
            [
                'name'            => 'Mortgage, Cooperative & Family Protection Clinic',
                'category'        => 'financial',
                'days_ahead'      => 18,
                'hour'            => 12,
                'location'        => 'SBS Executive Lounge',
                'capacity'        => 50,
                'points_checkin'  => 25,
                'points_feedback' => 10,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-financial-clinic',
                'description'     => 'One-on-one advisory clinic on staff mortgage facilities, medical cover optimization, and education savings plans.',
            ],
            [
                'name'            => 'End-of-Year Staff Wellness Appreciation Gala',
                'category'        => 'social',
                'days_ahead'      => 26,
                'hour'            => 16,
                'location'        => 'Student Centre Amphitheatre',
                'capacity'        => 120,
                'points_checkin'  => 35,
                'points_feedback' => 15,
                'comlink'         => 'https://whatsapp.com/channel/strathmore-wellness-gala',
                'description'     => 'Celebration of top wellness champions, departmental participation awards, and live acoustic performances.',
            ],
        ];

        foreach ($upcomingEventCatalog as $upcomingSpec) {
            $futureDate = Carbon::now()->addDays($upcomingSpec['days_ahead'])->setTime($upcomingSpec['hour'], 0, 0);

            $upcomingEvent = Event::firstOrCreate(
                ['name' => $upcomingSpec['name']],
                [
                    'category'        => $upcomingSpec['category'],
                    'starts_at'       => $futureDate,
                    'location'        => $upcomingSpec['location'],
                    'capacity'        => $upcomingSpec['capacity'],
                    'points_checkin'  => $upcomingSpec['points_checkin'],
                    'points_feedback' => $upcomingSpec['points_feedback'],
                    'comlink'         => $upcomingSpec['comlink'],
                    'description'     => $upcomingSpec['description'],
                    'created_by'      => $hrAdmin->id,
                    'created_at'      => Carbon::now()->subDays(2),
                ]
            );

            $selectedIndices = array_unique(array_merge(
                $upcomingSpec['days_ahead'] <= 11 ? [0] : [],
                (array) array_rand($staffUsers, rand(10, 15))
            ));

            foreach ($selectedIndices as $idx) {
                $participant = $staffUsers[$idx];
                $regCreatedAt = Carbon::now()->subHours(rand(2, 48));

                $registration = Registration::firstOrCreate(
                    [
                        'user_id'  => $participant->id,
                        'event_id' => $upcomingEvent->id,
                    ],
                    [
                        'status'        => 'registered',
                        'consent_given' => true,
                        'created_at'    => $regCreatedAt,
                    ]
                );

                Waiver::firstOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'signature_name' => $participant->name,
                        'signed_at'      => (clone $regCreatedAt)->addMinutes(2),
                    ]
                );

                $waiverTx = PointsTransaction::firstOrCreate(
                    [
                        'user_id'         => $participant->id,
                        'registration_id' => $registration->id,
                        'reason'          => 'waiver_signed',
                    ],
                    [
                        'points'     => 5,
                        'created_at' => (clone $regCreatedAt)->addMinutes(2),
                    ]
                );
                if ($waiverTx->wasRecentlyCreated) {
                    $participant->increment('points_balance', 5);
                }

                $preCheerful   = rand(1, 3);
                $preCalm       = rand(1, 3);
                $preActive     = rand(1, 3);
                $preRested     = rand(1, 2);
                $preInterested = rand(1, 3);

                WellbeingResponse::firstOrCreate(
                    [
                        'registration_id' => $registration->id,
                        'stage'           => 'pre',
                    ],
                    [
                        'item_cheerful'   => $preCheerful,
                        'item_calm'       => $preCalm,
                        'item_active'     => $preActive,
                        'item_rested'     => $preRested,
                        'item_interested' => $preInterested,
                        'total_score'     => $preCheerful + $preCalm + $preActive + $preRested + $preInterested,
                        'created_at'      => $regCreatedAt,
                    ]
                );
            }
        }

        Cache::flush();
    }
}