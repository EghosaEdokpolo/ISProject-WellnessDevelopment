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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Clean up ALL old data safely to reset IDs back to 1
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PointsTransaction::truncate();
        WellbeingResponse::truncate();
        Feedback::truncate();
        CheckIn::truncate();
        Waiver::truncate();
        Registration::truncate();
        Event::truncate();
        User::truncate(); // This resets the auto-increment to 1
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Create the HR Coordinator (This will get ID = 1, Staff ID = 000001)
        $hr = User::create([
            'name'           => 'Grace Wanjiru',
            'staff_id'       => '000001',
            'email'          => 'hr.admin@strathmore.edu',
            'password'       => Hash::make('password'),
            'role'           => 'hr_coordinator',
            'department'     => 'Human Resources',
            'points_balance' => 0,
        ]);

        // 3. Curated Lists of Authentic Kenyan Names
        $firstNames = [
            'Brian', 'Kevin', 'Dennis', 'Peter', 'John', 'James', 'David', 'Samuel', 'Joseph', 'Daniel',
            'Michael', 'Charles', 'Thomas', 'George', 'Paul', 'Eric', 'Collins', 'Victor', 'Anthony', 'Vincent',
            'Faith', 'Mercy', 'Esther', 'Naomi', 'Ruth', 'Sarah', 'Rebecca', 'Rachel', 'Leah',
            'Hannah', 'Deborah', 'Mary', 'Martha', 'Elizabeth', 'Catherine', 'Anne', 'Jane', 'Lucy', 'Rose',
            'Achieng', 'Atieno', 'Chebet', 'Wambui', 'Njeri', 'Muthoni', 'Nyambura', 'Akinyi', 'Adhiambo', 'Kerubo'
        ];
        
        $lastNames = [
            'Otieno', 'Omondi', 'Ochieng', 'Onyango', 'Ouma', 'Ogutu', 'Okoth', 'Obonyo', 'Odhiambo', 'Omollo',
            'Mutiso', 'Kimani', 'Kariuki', 'Mwangi', 'Kamau', 'Njoroge', 'Wanjiru', 'Wairimu', 'Wambui', 'Chebet',
            'Kiptoo', 'Korir', 'Langat', 'Rotich', 'Sang', 'Cheruiyot', 'Wafula', 'Wanjala', 'Barasa', 'Simiyu',
            'Wekesa', 'Wamalwa', 'Muthama', 'Mutua', 'Musyoka', 'Kilonzo', 'Ngugi', 'Ndungu', 'Gitau', 'Maina'
        ];

        $departments = [
            'School of Computing and Engineering Sciences',
            'Strathmore Business School',
            'School of Law',
            'School of Humanities and Social Sciences',
            'Finance and Accounting',
            'Human Resources'
        ];

        // 4. Generate 50 Staff Members (They will get IDs 2-51)
        $staff = collect();
        
        for ($i = 0; $i < 50; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $fullName = "{$firstName} {$lastName}";
            
            $email = strtolower($firstName . '.' . $lastName) . rand(10, 99) . '@strathmore.edu';

            // Create with a temporary staff_id to satisfy the database
            $user = User::create([
                'name'           => $fullName,
                'staff_id'       => 'temp-' . Str::random(5),
                'email'          => $email,
                'password'       => Hash::make('Strathmore2026!'),
                'role'           => 'staff',
                'department'     => $departments[array_rand($departments)],
                'points_balance' => 0,
            ]);

            // Now grab the auto-generated ID and format it as the staff_id
            $user->staff_id = str_pad((string) $user->id, 6, '0', STR_PAD_LEFT);
            $user->save();
            
            $staff->push($user);
        }

        // 5. Create 5 Past Events
        $events = collect();
        $categories = ['physical', 'mental', 'financial', 'social'];
        $eventTitles = [
            'Mental Health Awareness Talk', 'Staff Yoga & Mindfulness', 
            'Financial Literacy Workshop', 'Team Building Retreat',
            'Health & Nutrition Seminar', 'Stress Management Workshop'
        ];
        
        for ($i = 1; $i <= 5; $i++) {
            $events->push(Event::create([
                'name'             => $eventTitles[array_rand($eventTitles)],
                'category'         => $categories[array_rand($categories)],
                'description'      => 'A wellness initiative organized by Strathmore HR.',
                'starts_at'        => Carbon::now()->subDays(rand(5, 30)),
                'location'         => 'Main Campus - Auditorium',
                'capacity'         => 80,
                'created_by'       => $hr->id,
            ]));
        }

        // 6. Simulate Interactions
        foreach ($events as $event) {
            $registeredStaff = $staff->random(rand(25, 40));

            foreach ($registeredStaff as $person) {
                $reg = Registration::create([
                    'user_id'       => $person->id,
                    'event_id'      => $event->id,
                    'status'        => 'registered',
                    'consent_given' => true,
                ]);

                // 90% sign waiver
                if (rand(1, 100) <= 90) {
                    Waiver::create([
                        'registration_id' => $reg->id,
                        'signature_name'  => $person->name,
                        'signed_at'       => now(),
                    ]);
                    $this->awardPoints($person, $reg, 'waiver_signed', 5);
                }

                // 80% check in
                if (rand(1, 100) <= 80) {
                    CheckIn::create([
                        'registration_id' => $reg->id,
                        'checked_in_at'   => $event->starts_at->copy()->addMinutes(rand(0, 30)),
                        'method'          => 'qr',
                    ]);
                    $reg->update(['status' => 'checked_in']);
                    $this->awardPoints($person, $reg, 'attended_event', 20);

                    // 70% complete surveys
                    if (rand(1, 100) <= 70) {
                        $preScore = rand(6, 12); 
                        $postScore = min(25, $preScore + rand(2, 8)); 

                        WellbeingResponse::create([
                            'registration_id' => $reg->id, 'stage' => 'pre',
                            'item_cheerful' => rand(1,3), 'item_calm' => rand(1,3), 'item_active' => rand(1,3),
                            'item_rested' => rand(1,3), 'item_interested' => rand(1,3), 'total_score' => $preScore,
                        ]);

                        WellbeingResponse::create([
                            'registration_id' => $reg->id, 'stage' => 'post',
                            'item_cheerful' => rand(2,4), 'item_calm' => rand(2,4), 'item_active' => rand(2,4),
                            'item_rested' => rand(2,4), 'item_interested' => rand(2,4), 'total_score' => $postScore,
                        ]);

                        // 80% leave feedback
                        if (rand(1, 100) <= 80) {
                            $feedbackTexts = [
                                'Really enjoyed the session, very refreshing.',
                                'Great initiative. Would love more of these.',
                                'The facilitator was excellent.',
                                'Useful content, well organised.',
                                'Nice to connect with colleagues outside work.',
                                null
                            ];
                            
                            Feedback::create([
                                'registration_id'    => $reg->id,
                                'satisfaction_stars' => rand(3, 5),
                                'social_quality'     => rand(6, 10),
                                'comments'           => $feedbackTexts[array_rand($feedbackTexts)],
                            ]);
                            $reg->update(['status' => 'completed']);
                            $this->awardPoints($person, $reg, 'feedback_submitted', 10);
                        }
                    }
                }
            }
        }

        $this->command->info('Success! Database reset. Created 1 HR Admin (ID 1) and 50 Staff (IDs 2-51) with matching Staff IDs.');
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