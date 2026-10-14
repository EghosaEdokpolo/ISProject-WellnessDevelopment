<?php

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

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $hr = User::firstOrCreate(
            ['staff_id' => '000001'],
            [
                'name'        => 'Grace Wanjiru',
                'password'    => Hash::make('password'),
                'role'        => 'hr_coordinator',
                'department'  => 'Human Resources',
                'employee_id' => 'EMP-001',
            ]
        );

        $staffData = [
            ['Achieng Otieno',   'School of Computing and Engineering Sciences'],
            ['Brian Mutiso',     'Finance and Accounting'],
            ['Naomi Chebet',     'School of Law'],
            ['Jane Wanjiku',     'Strathmore Business School'],
            ['David Kimani',     'School of Computing and Engineering Sciences'],
            ['Grace Njeri',      'Human Resources'],
            ['Peter Omondi',     'School of Humanities and Social Sciences'],
            ['Faith Muthoni',    'Finance and Accounting'],
            ['Kevin Kipchoge',   'Cafeteria Staff'],
            ['Sarah Atieno',     'Housekeeping'],
            ['Michael Kamau',    'School of Law'],
            ['Mercy Wambui',     'Strathmore Business School'],
            ['Samuel Ochieng',   'School of Computing and Engineering Sciences'],
            ['Esther Nyambura',  'Human Resources'],
            ['Dennis Mwangi',    'School of Humanities and Social Sciences'],
            ['Lydia Akinyi',     'Finance and Accounting'],
            ['James Kariuki',    'Cafeteria Staff'],
            ['Rose Wairimu',     'Housekeeping'],
            ['Anthony Njoroge',  'School of Law'],
            ['Lucy Wanjiru',     'Strathmore Business School'],
            ['Daniel Maina',     'School of Computing and Engineering Sciences'],
            ['Catherine Njoki',  'Human Resources'],
            ['Eric Wekesa',      'School of Humanities and Social Sciences'],
            ['Pauline Adhiambo', 'Finance and Accounting'],
            ['Timothy Barasa',   'Cafeteria Staff'],
            ['Miriam Kerubo',    'Housekeeping'],
            ['Joseph Muthama',   'School of Law'],
            ['Hellen Wangui',    'Strathmore Business School'],
            ['Charles Odhiambo', 'School of Computing and Engineering Sciences'],
            ['Irene Nasimiyu',   'Human Resources'],
        ];

        $staff = collect();
        foreach ($staffData as $i => [$name, $dept]) {
            $num = 100 + $i;
            $sid = str_pad((string) $num, 6, '0', STR_PAD_LEFT);
            $staff->push(User::firstOrCreate(
                ['staff_id' => $sid],
                [
                    'name'        => $name,
                    'password'    => Hash::make('password'),
                    'role'        => 'staff',
                    'department'  => $dept,
                    'employee_id' => 'EMP-' . $num,
                ]
            ));
        }

        $now = Carbon::now();

        $eventData = [
            ['name' => 'Staff Yoga & Mindfulness Session', 'category' => 'mental',    'starts_at' => $now->copy()->subDays(45)->setTime(9, 0),  'location' => 'Sports Complex Studio B',   'capacity' => 40,  'points_awarded' => 15, 'description' => 'Guided yoga followed by guided meditation.'],
            ['name' => 'Mental Health Awareness Talk',    'category' => 'mental',    'starts_at' => $now->copy()->subDays(38)->setTime(14, 0), 'location' => 'Auditorium Main Hall',      'capacity' => 120, 'points_awarded' => 10, 'description' => 'A talk on recognising burnout and seeking help.'],
            ['name' => 'Staff Hike Karura Forest',        'category' => 'physical',  'starts_at' => $now->copy()->subDays(30)->setTime(7, 0),  'location' => 'Karura Forest Entrance',    'capacity' => 50,  'points_awarded' => 20, 'description' => 'A guided 8 km hike through Karura Forest.'],
            ['name' => 'Financial Literacy Workshop',      'category' => 'financial', 'starts_at' => $now->copy()->subDays(21)->setTime(11, 0), 'location' => 'SBS Room 204',              'capacity' => 60,  'points_awarded' => 10, 'description' => 'Budgeting, savings, and investment basics.'],
            ['name' => 'Mid-Semester Wellness Check-in',   'category' => 'mental',    'starts_at' => $now->copy()->subDays(7)->setTime(15, 0),  'location' => 'Wellness Centre',           'capacity' => 30,  'points_awarded' => 10, 'description' => 'Drop-in wellbeing assessments.'],
            ['name' => 'Team Building Retreat',            'category' => 'social',    'starts_at' => $now->copy()->addDays(10)->setTime(8, 0),  'location' => 'Naivasha Sopa Resort',      'capacity' => 80,  'points_awarded' => 25, 'description' => 'A full-day team building retreat.'],
            ['name' => 'Zumba & Aerobics Night',           'category' => 'physical',  'starts_at' => $now->copy()->addDays(18)->setTime(18, 0), 'location' => 'Sports Complex Main Court', 'capacity' => 60,  'points_awarded' => 15, 'description' => 'High-energy group fitness session.'],
            ['name' => 'Staff Wellness Fair',              'category' => 'social',    'starts_at' => $now->copy()->addDays(35)->setTime(10, 0), 'location' => 'Main Courtyard',            'capacity' => 200, 'points_awarded' => 10, 'description' => 'Health screenings, nutrition advice, wellness booths.'],
        ];

        $events = collect();
        foreach ($eventData as $data) {
            $events->push(Event::create(array_merge($data, [
                'created_by'   => $hr->id,
                'is_published' => true,
                'published_at' => $data['starts_at']->copy()->subDays(7),
            ])));
        }

        foreach ($events as $event) {
            $isPast = $event->starts_at->isPast();
            $registeredStaff = $staff->random(rand(12, 22));

            foreach ($registeredStaff as $person) {
                $registration = Registration::firstOrCreate(
                    ['user_id' => $person->id, 'event_id' => $event->id],
                    ['status' => 'registered', 'created_at' => $event->starts_at->copy()->subDays(rand(5, 20))]
                );

                $signsWaiver = rand(1, 100) <= 90;
                if ($signsWaiver) {
                    Waiver::firstOrCreate(
                        ['registration_id' => $registration->id],
                        ['signature_name' => $person->name, 'signed_at' => $registration->created_at->copy()->addHours(rand(2, 72))]
                    );
                    $this->awardPoints($person, $registration, 'waiver_signed', 5);
                }

                if ($isPast && $signsWaiver && rand(1, 100) <= 75) {
                    CheckIn::firstOrCreate(
                        ['registration_id' => $registration->id],
                        ['checked_in_at' => $event->starts_at->copy()->addMinutes(rand(0, 20)), 'method' => rand(1, 100) <= 85 ? 'qr' : 'manual']
                    );
                    $registration->update(['status' => 'checked_in']);
                    $this->awardPoints($person, $registration, 'attended_event', 10);

                    if (rand(1, 100) <= 85) {
                        $preScore  = rand(8, 14);
                        $postScore = min(25, $preScore + rand(1, 6));

                        WellbeingResponse::firstOrCreate(
                            ['registration_id' => $registration->id, 'stage' => 'pre'],
                            array_merge(['total_score' => $preScore], $this->randomItemsForScore($preScore))
                        );
                        WellbeingResponse::firstOrCreate(
                            ['registration_id' => $registration->id, 'stage' => 'post'],
                            array_merge(['total_score' => $postScore], $this->randomItemsForScore($postScore))
                        );

                        if (rand(1, 100) <= 80) {
                            Feedback::firstOrCreate(
                                ['registration_id' => $registration->id],
                                ['satisfaction_stars' => rand(3, 5), 'social_quality' => rand(6, 10), 'comments' => null]
                            );
                            $registration->update(['status' => 'completed']);
                            $this->awardPoints($person, $registration, 'feedback_submitted', 5);
                        }
                    }
                }
            }
        }

        foreach (User::where('role', 'staff')->get() as $u) {
            $u->update(['points_balance' => PointsTransaction::where('user_id', $u->id)->sum('points')]);
        }

        $this->command->info('Demo data seeded.');
    }

    private function awardPoints(User $user, Registration $registration, string $reason, int $points): void
    {
        if (PointsTransaction::where('registration_id', $registration->id)->where('reason', $reason)->exists()) return;
        PointsTransaction::create(['user_id' => $user->id, 'registration_id' => $registration->id, 'reason' => $reason, 'points' => $points]);
    }

    private function randomItemsForScore(int $total): array
    {
        $items = [0, 0, 0, 0, 0];
        for ($i = 0; $i < $total; $i++) $items[$i % 5]++;
        shuffle($items);
        return ['item_cheerful' => $items[0], 'item_calm' => $items[1], 'item_active' => $items[2], 'item_rested' => $items[3], 'item_interested' => $items[4]];
    }
}