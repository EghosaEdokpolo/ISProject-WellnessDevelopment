<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// 1. IMPORT ALL THE RELATED DATA MODELS AT THE TOP
use App\Models\User;
use App\Models\Event;
use App\Models\Registration;
use App\Models\WellbeingResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ------------------------------------------------------------------
        // STEP A: CREATE THE DEFAULT HR COORDINATOR LOGIN ACCOUNT
        // ------------------------------------------------------------------
        // We look for 'hr_coordinator' depending on your schema rules
        $hrAdmin = User::where('role', 'hr_coordinator')->first();
        
        if (!$hrAdmin) {
            // We save the newly built user directly into our active $hrAdmin variable link!
            $hrAdmin = User::firstOrCreate(
                ['staff_id' => 100010], 
                [
                    'name' => 'HR Coordinator',
                    'role' => 'hr_coordinator', 
                    'password' => Hash::make('password'),
                ]
            );
        }

        // ------------------------------------------------------------------
        // STEP B: GENERATE DUMMY EVENTS ACROSS ALL 4 WELLNESS CATEGORIES
        // ------------------------------------------------------------------
        $categories = ['physical', 'mental', 'financial', 'social'];
        $eventNames = [
            'Yoga & Mindfulness Session',
            'Mental Health Awareness Talk',
            'Personal Finance Workshop',
            'Team Building Social Mixer'
        ];

        foreach ($eventNames as $index => $name) {
            $category = $categories[$index] ?? 'physical';
            
            // Creates the event if it doesn't find it in the table rows register
            $event = Event::firstOrCreate(
                ['name' => $name],
                [
                    'category'   => $category,
                    'starts_at'  => Carbon::now()->addHours(2),
                    'location'   => 'Strathmore University Main Campus', 
                    'capacity'   => rand(50, 100),
                    
                    // 🌟 DYNAMIC FIX: Pass down the tracking ID of the HR user who created it!
                    'created_by' => $hrAdmin->id, 
                    
                    'created_at' => Carbon::now()->startOfMonth(),
                ]
            );
            // --------------------------------------------------------------
            // STEP C: GENERATE UNIQUE STAFF MEMBERS FOR EACH EVENT ACTIVITY
            // --------------------------------------------------------------
            for ($i = 1; $i <= 5; $i++) {
                
                // 🌟 CHANGED: Removed "STF-" prefix string, using a pure random number instead!
                $staffId = rand(20000, 99999); 
                
                $staffUser = User::firstOrCreate(
                    ['staff_id' => $staffId],
                    [
                        'name' => 'Staff Member ' . rand(1, 100),
                        'role' => 'staff',
                        'password' => Hash::make('password'),
                        'points_balance' => rand(10, 50),
                    ]
                );

                // Create the core active user registration transaction link row
                $registration = Registration::create([
                    'user_id'    => $staffUser->id,
                    'event_id'   => $event->id,
                    'status'     => 'completed',
                    
                    // 🌟 REMOVED/COMMENTED OUT THIS LINE: It doesn't exist on your registrations table!
                    // 'feedback_submitted' => true, 
                    
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                ]);

                // --------------------------------------------------------------
                // STEP D: SEED PRE-EVENT SURVEY SUBMISSIONS (Lower wellness baselines)
                // --------------------------------------------------------------
                $preCheerful   = rand(1, 2);
                $preCalm       = rand(0, 2);
                $preActive     = rand(1, 2);
                $preRested     = rand(0, 1);
                $preInterested = rand(1, 2);
                $preTotal      = $preCheerful + $preCalm + $preActive + $preRested + $preInterested;

                WellbeingResponse::create([
                    'registration_id' => $registration->id,
                    'stage' => 'pre',
                    'item_cheerful' => $preCheerful,
                    'item_calm' => $preCalm,
                    'item_active' => $preActive,
                    'item_rested' => $preRested,
                    'item_interested' => $preInterested,
                    'total_score' => $preTotal,
                ]);

                // --------------------------------------------------------------
                // STEP E: SEED POST-EVENT SURVEY SUBMISSIONS (Showing growth lift metrics)
                // --------------------------------------------------------------
                $postCheerful   = rand(3, 4);
                $postCalm       = rand(3, 4);
                $postActive     = rand(2, 4);
                $postRested     = rand(2, 4);
                $postInterested = rand(3, 4);
                $postTotal      = $postCheerful + $postCalm + $postActive + $postRested + $postInterested;

                WellbeingResponse::create([
                    'registration_id' => $registration->id,
                    'stage' => 'post',
                    'item_cheerful' => $postCheerful,
                    'item_calm' => $postCalm,
                    'item_active' => $postActive,
                    'item_rested' => $postRested,
                    'item_interested' => $postInterested,
                    'total_score' => $postTotal,
                ]);
            }
        }
    }
}
