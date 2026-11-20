<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventGuest;
use App\Models\Registration;
use App\Models\User;
use App\Models\Waiver;
use App\Services\CheckInService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class CheckInController extends Controller
{
    public function __construct(
        private readonly CheckInService $checkInService
    ) {}

    public function index(Request $request): View
    {
        $user             = $request->user();
        $tz               = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $lateGraceMinutes = (int) config('wellloop.checkin.late_grace_minutes', 15);
        $cutoff           = now($tz)->subMinutes($lateGraceMinutes);

        $registrations = Registration::with(['event', 'waiver', 'checkIn', 'feedback'])
            ->where('user_id', $user->id)
            ->get()
            ->filter(fn (Registration $registration): bool => $registration->event !== null)
            ->values();

        foreach ($registrations as $reg) {
            if (! $reg->waiver && $reg->consent_given) {
                $waiver = Waiver::firstOrCreate(
                    ['registration_id' => $reg->id],
                    [
                        'signature_name' => $user->name,
                        'signed_at'      => $reg->created_at ?? now(),
                    ]
                );
                $reg->setRelation('waiver', $waiver);
            }
        }

        $activeRegistrations = $registrations
            ->filter(function (Registration $registration) use ($tz, $cutoff): bool {
                if ($registration->checkIn !== null || in_array($registration->status, ['checked_in', 'completed'], true)) {
                    return false;
                }

                $startsAt = $registration->event->starts_at?->copy()->shiftTimezone($tz);
                return $startsAt !== null && $startsAt->gt($cutoff);
            })
            ->sortBy(fn (Registration $registration) => $registration->event->starts_at->timestamp)
            ->values();

        $pastCheckIns = $registrations
            ->filter(fn (Registration $registration): bool => $registration->checkIn !== null || in_array($registration->status, ['checked_in', 'completed'], true))
            ->sortByDesc(fn (Registration $registration) => $registration->checkIn?->checked_in_at?->timestamp ?? $registration->event->starts_at?->timestamp ?? 0)
            ->values();

        $upcomingEvents = Event::where('starts_at', '>', $cutoff)
            ->orderBy('starts_at', 'asc')
            ->get();

        return view('checkin.index', [
            'user'                => $user,
            'activeRegistrations' => $activeRegistrations,
            'pastCheckIns'        => $pastCheckIns,
            'upcomingEvents'      => $upcomingEvents,
            'totalCheckIns'       => $pastCheckIns->count(),
            'pointsBalance'       => (int) ($user->points_balance ?? 0),
        ]);
    }

    public function poster(Event $event): View
    {
        $scanUrl = URL::signedRoute('checkin.scan', ['event' => $event->id]);

        return view('events.qr_poster', compact('event', 'scanUrl'));
    }

    public function scan(Request $request, Event $event): View
    {
        $user = $request->user();
        $tz   = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $gate = $this->checkInService->evaluateGateStatus($event);

        // 1. Logged-in HR Coordinator accessing Session Attendance Register
        if ($user !== null && $user->isHrCoordinator() && ! $request->hasValidSignature()) {
            $search = trim((string) $request->query('search', ''));

            $query = Registration::with(['user', 'checkIn', 'waiver', 'feedback'])
                ->where('event_id', $event->id)
                ->whereHas('user');

            if ($search !== '') {
                $query->whereHas('user', function ($q) use ($search): void {
                    $q->where(fn ($sub) => $sub
                        ->where('staff_id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('department', 'like', "%{$search}%"));
                });
            }

            $registrations = $query->get()
                ->sort(fn (Registration $a, Registration $b): int => strnatcasecmp((string) ($a->user->staff_id ?? ''), (string) ($b->user->staff_id ?? '')))
                ->values();

            $guestCheckIns = collect();
            if (Schema::hasTable('event_guests')) {
                $guestQuery = EventGuest::where('event_id', $event->id);
                if ($search !== '') {
                    $guestQuery->where(function ($q) use ($search): void {
                        $q->where('full_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('phone', 'like', "%{$search}%")
                          ->orWhere('organization', 'like', "%{$search}%")
                          ->orWhere('guest_type', 'like', "%{$search}%");
                    });
                }
                $guestCheckIns = $guestQuery->orderBy('checked_in_at', 'asc')->get();
            }

            $checkedInCount  = $registrations->filter(fn (Registration $r) => $r->checkIn !== null || in_array($r->status, ['checked_in', 'completed'], true))->count();
            $registeredCount = $registrations->count();

            return view('checkin.scan', [
                'event'           => $event,
                'tz'              => $tz,
                'gate'            => $gate,
                'registrations'   => $registrations,
                'guestCheckIns'   => $guestCheckIns,
                'guestCount'      => $guestCheckIns->count(),
                'checkedInCount'  => $checkedInCount,
                'registeredCount' => $registeredCount,
                'search'          => $search,
                'myRegistration'  => null,
            ]);
        }

        // All venue QR check-ins require the signed QR poster URL
        abort_unless($request->hasValidSignature(), 403, 'Please scan the official QR poster at the event venue to check in.');

        // 2. Unauthenticated User (Staff OR External Guest)
        if ($user === null) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => $request->query('mode') === 'guest' ? 'guest' : 'staff',
            ]));
        }

        // 3. Already Logged-In Staff Member: Instant check-in
        try {
            $outcome = $this->checkInService->processVenueQrScan($user, $event);

            return view('events.qr_scan_result', [
                'event'            => $event,
                'registration'     => $outcome['registration'],
                'autoRegistered'   => $outcome['auto_registered'],
                'alreadyCheckedIn' => $outcome['already_checked_in'],
                'pointsEarned'     => $outcome['points_earned'],
                'isLate'           => $outcome['is_late'],
                'minutesLate'      => $outcome['minutes_late'],
                'basePoints'       => $outcome['base_points'],
                'pointsReducedBy'  => $outcome['points_reduced_by'],
                'needsOnboarding'  => $outcome['needs_onboarding'],
                'scanError'        => null,
            ]);
        } catch (RuntimeException $e) {
            return view('events.qr_scan_result', [
                'event'            => $event,
                'registration'     => null,
                'autoRegistered'   => false,
                'alreadyCheckedIn' => false,
                'pointsEarned'     => 0,
                'isLate'           => false,
                'minutesLate'      => 0,
                'basePoints'       => (int) ($event->points_checkin ?? 20),
                'pointsReducedBy'  => 0,
                'needsOnboarding'  => false,
                'scanError'        => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Unexpected failure during venue QR scan', [
                'user_id'  => $user->id,
                'event_id' => $event->id,
                'error'    => $e->getMessage(),
            ]);

            return view('events.qr_scan_result', [
                'event'            => $event,
                'registration'     => null,
                'autoRegistered'   => false,
                'alreadyCheckedIn' => false,
                'pointsEarned'     => 0,
                'isLate'           => false,
                'minutesLate'      => 0,
                'basePoints'       => (int) ($event->points_checkin ?? 20),
                'pointsReducedBy'  => 0,
                'needsOnboarding'  => false,
                'scanError'        => 'We could not complete your check-in right now. Please notify the People & Culture desk at the venue.',
            ]);
        }
    }

    /**
     * Tab 1: Verify Strathmore Staff ID without forcing a login redirect.
     */
    public function verifyStaffScan(Request $request, Event $event): View|RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403, 'Invalid or expired venue QR check-in signature.');

        $gate = $this->checkInService->evaluateGateStatus($event);

        $data = $request->validate([
            'staff_id' => ['required', 'string', 'max:50'],
        ], [
            'staff_id.required' => 'Please enter your Strathmore Staff ID to verify your attendance.',
        ]);

        $normalizedStaffId = strtolower(trim($data['staff_id']));
        $attendee          = User::whereRaw('LOWER(staff_id) = ?', [$normalizedStaffId])->first();

        if (! $attendee) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'staff',
                'scanError' => 'Staff ID "' . e($data['staff_id']) . '" was not found in the Strathmore directory. If you are an external visitor, speaker, or new hire without a Staff ID yet, switch to the "External Visitor / Guest" tab above.',
            ]));
        }

        try {
            $outcome = $this->checkInService->processVenueQrScan($attendee, $event);

            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'staff',
                'outcome'   => $outcome,
                'attendee'  => $attendee->fresh(),
            ]));
        } catch (RuntimeException $e) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'staff',
                'attendee'  => $attendee,
                'scanError' => $e->getMessage(),
            ]));
        } catch (\Throwable $e) {
            Log::error('Unexpected failure during guest Staff ID QR check-in', [
                'staff_id' => $normalizedStaffId,
                'event_id' => $event->id,
                'error'    => $e->getMessage(),
            ]);

            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'staff',
                'attendee'  => $attendee,
                'scanError' => 'We could not complete your check-in right now. Please notify the People & Culture desk at the venue.',
            ]));
        }
    }

    /**
     * Tab 2 (Step 1): Validate External Visitor details & KDPA 2019 consent, then dispatch a 6-digit Email OTP.
     */
    public function guestCheckIn(Request $request, Event $event): View
    {
        abort_unless($request->hasValidSignature(), 403, 'Invalid or expired venue QR check-in signature.');

        $gate        = $this->checkInService->evaluateGateStatus($event);
        $guestWindow = $this->evaluateGuestWindow($gate);

        if (! $guestWindow['is_open']) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'guest',
                'scanError' => $guestWindow['message'],
            ]));
        }

        $data = $request->validate([
            'full_name'       => ['required', 'string', 'max:150'],
            'email'           => ['required', 'email', 'max:150'],
            'phone'           => ['required', 'string', 'max:30'],
            'guest_type'      => ['required', 'string', Rule::in(['facilitator', 'partner_vendor', 'visiting_faculty', 'staff_family', 'new_hire_pending', 'external_visitor'])],
            'organization'    => ['nullable', 'string', 'max:150'],
            'invited_by'      => ['nullable', 'string', 'max:150'],
            'waiver_accepted' => ['accepted'],
        ], [
            'full_name.required'       => 'Please enter your full name.',
            'email.required'           => 'Please enter a valid email address so we can send your 6-digit verification code.',
            'phone.required'           => 'Please enter your phone number for campus safety records.',
            'waiver_accepted.accepted' => 'You must review and accept the Kenya Data Protection Act (2019) notice to proceed.',
        ]);

        $normalizedEmail = strtolower(trim($data['email']));
        $normalizedPhone = preg_replace('/\s+/', '', trim($data['phone']));

        // Smart Guard: Redirect staff who accidentally used the Guest tab back to Staff tab so they earn points
        $existingStaff = User::whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();
        if ($existingStaff && $existingStaff->role === 'staff') {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'staff',
                'scanError' => "We recognized {$existingStaff->name} ({$existingStaff->email}) as a Strathmore staff member! Please check in using your Staff ID on the \"Strathmore Staff\" tab so you earn your wellness points.",
            ]));
        }

        // Already checked in as a verified guest for this session
        $existingGuest = EventGuest::where('event_id', $event->id)
            ->where(function ($q) use ($normalizedEmail, $normalizedPhone): void {
                $q->whereRaw('LOWER(email) = ?', [$normalizedEmail])
                  ->orWhere('phone', $normalizedPhone);
            })
            ->first();

        if ($existingGuest) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab'    => 'guest',
                'guestOutcome' => [
                    'guest'              => $existingGuest,
                    'already_checked_in' => true,
                ],
            ]));
        }

        // Generate cryptographically secure 6-digit OTP & store hashed in Cache (10-min TTL)
        $otpCode   = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpToken  = (string) Str::uuid();
        $expiresAt = now()->addMinutes(10);

        $pendingRecord = [
            'event_id'            => $event->id,
            'full_name'           => trim($data['full_name']),
            'email'               => $normalizedEmail,
            'phone'               => $normalizedPhone,
            'guest_type'          => $data['guest_type'],
            'organization'        => isset($data['organization']) && trim($data['organization']) !== '' ? trim($data['organization']) : null,
            'invited_by'          => isset($data['invited_by']) && trim($data['invited_by']) !== '' ? trim($data['invited_by']) : null,
            'ip_address'          => $request->ip(),
            'otp_hash'            => Hash::make($otpCode),
            'attempts'            => 0,
            'expires_at'          => $expiresAt->timestamp,
            'resend_available_at' => now()->addSeconds(60)->timestamp,
        ];

        Cache::put($this->guestOtpCacheKey($event->id, $otpToken), $pendingRecord, $expiresAt);

        $delivery = $this->dispatchGuestOtpEmail($normalizedEmail, $pendingRecord['full_name'], $event, $otpCode);

        return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
            'activeTab'   => 'guest',
            'pendingOtp'  => [
                'token'               => $otpToken,
                'full_name'           => $pendingRecord['full_name'],
                'masked_email'        => $this->maskEmail($normalizedEmail),
                'expires_at_iso'      => $expiresAt->toIso8601String(),
                'resend_wait_seconds' => 60,
                'delivered_via_smtp'  => $delivery['sent'],
                'fallback_notice'     => $delivery['fallback_notice'],
                'demo_otp'            => $delivery['demo_otp'],
            ],
            'scanNotice'  => $delivery['sent']
                ? 'We sent a 6-digit verification code to ' . $this->maskEmail($normalizedEmail) . '. Enter it below to complete your check-in.'
                : null,
        ]));
    }

    /**
     * Tab 2 (Step 2): Verify the 6-digit Email OTP and save the verified External Visitor to MySQL.
     */
    public function verifyGuestOtp(Request $request, Event $event): View
    {
        abort_unless($request->hasValidSignature(), 403, 'Invalid or expired venue QR check-in signature.');

        $tz   = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $gate = $this->checkInService->evaluateGateStatus($event);

        $validator = Validator::make($request->all(), [
            'otp_token' => ['required', 'string', 'max:100'],
            'otp_code'  => ['required', 'digits:6'],
        ], [
            'otp_code.required' => 'Please enter the 6-digit verification code sent to your email.',
            'otp_code.digits'   => 'The verification code must be exactly 6 numeric digits.',
        ]);

        $token    = (string) $request->input('otp_token', '');
        $cacheKey = $this->guestOtpCacheKey($event->id, $token);
        $record   = $token !== '' ? Cache::get($cacheKey) : null;

        if (! is_array($record)) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'guest',
                'scanError' => 'Your verification code has expired (10-minute limit). Please fill in your visitor details again to request a new code.',
            ]));
        }

        $pendingViewState = [
            'token'               => $token,
            'full_name'           => $record['full_name'],
            'masked_email'        => $this->maskEmail($record['email']),
            'expires_at_iso'      => now()->setTimestamp((int) $record['expires_at'])->toIso8601String(),
            'resend_wait_seconds' => max(0, (int) $record['resend_available_at'] - time()),
            'delivered_via_smtp'  => true,
            'fallback_notice'     => null,
            'demo_otp'            => null,
        ];

        if ($validator->fails()) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab'  => 'guest',
                'pendingOtp' => $pendingViewState,
                'scanError'  => $validator->errors()->first(),
            ]));
        }

        if ((int) ($record['attempts'] ?? 0) >= 5) {
            Cache::forget($cacheKey);

            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'guest',
                'scanError' => 'Too many incorrect OTP attempts. For security, please request a fresh verification code.',
            ]));
        }

        $enteredCode = trim((string) $request->input('otp_code'));

        if (! Hash::check($enteredCode, (string) $record['otp_hash'])) {
            $record['attempts'] = (int) ($record['attempts'] ?? 0) + 1;
            $remaining          = max(0, 5 - $record['attempts']);
            $ttlSeconds         = max(30, (int) $record['expires_at'] - time());
            Cache::put($cacheKey, $record, $ttlSeconds);

            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab'  => 'guest',
                'pendingOtp' => $pendingViewState,
                'scanError'  => $remaining > 0
                    ? "Incorrect 6-digit code. You have {$remaining} attempt" . ($remaining === 1 ? '' : 's') . " remaining."
                    : 'Incorrect code. Maximum attempts reached — please request a new verification code.',
            ]));
        }

        // OTP matched: consume token immediately (single-use) and persist verified visitor
        Cache::forget($cacheKey);

        $existingGuest = EventGuest::where('event_id', $event->id)
            ->where(function ($q) use ($record): void {
                $q->whereRaw('LOWER(email) = ?', [$record['email']])
                  ->orWhere('phone', $record['phone']);
            })
            ->first();

        if ($existingGuest) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab'    => 'guest',
                'guestOutcome' => [
                    'guest'              => $existingGuest,
                    'already_checked_in' => true,
                ],
            ]));
        }

        $payload = [
            'event_id'        => $event->id,
            'full_name'       => $record['full_name'],
            'email'           => $record['email'],
            'phone'           => $record['phone'],
            'guest_type'      => $record['guest_type'],
            'organization'    => $record['organization'],
            'invited_by'      => $record['invited_by'],
            'waiver_accepted' => true,
            'ip_address'      => $record['ip_address'] ?? $request->ip(),
            'checked_in_at'   => now($tz),
        ];

        if (Schema::hasColumn('event_guests', 'verification_method')) {
            $payload['verification_method'] = 'email_otp';
        }
        if (Schema::hasColumn('event_guests', 'verified_at')) {
            $payload['verified_at'] = now($tz);
        }

        $guest = EventGuest::create($payload);

        return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
            'activeTab'    => 'guest',
            'guestOutcome' => [
                'guest'              => $guest,
                'already_checked_in' => false,
            ],
        ]));
    }

    /**
     * Resend a fresh 6-digit Email OTP after the 60-second cooldown.
     */
    public function resendGuestOtp(Request $request, Event $event): View
    {
        abort_unless($request->hasValidSignature(), 403, 'Invalid or expired venue QR check-in signature.');

        $gate     = $this->checkInService->evaluateGateStatus($event);
        $token    = (string) $request->input('otp_token', '');
        $cacheKey = $this->guestOtpCacheKey($event->id, $token);
        $record   = $token !== '' ? Cache::get($cacheKey) : null;

        if (! is_array($record)) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab' => 'guest',
                'scanError' => 'Your verification session expired. Please re-enter your visitor details.',
            ]));
        }

        $waitRemaining = (int) ($record['resend_available_at'] ?? 0) - time();
        if ($waitRemaining > 0) {
            return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
                'activeTab'  => 'guest',
                'pendingOtp' => [
                    'token'               => $token,
                    'full_name'           => $record['full_name'],
                    'masked_email'        => $this->maskEmail($record['email']),
                    'expires_at_iso'      => now()->setTimestamp((int) $record['expires_at'])->toIso8601String(),
                    'resend_wait_seconds' => $waitRemaining,
                    'delivered_via_smtp'  => true,
                    'fallback_notice'     => null,
                    'demo_otp'            => null,
                ],
                'scanError'  => "Please wait {$waitRemaining} second(s) before requesting another verification code.",
            ]));
        }

        $otpCode   = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        $record['otp_hash']            = Hash::make($otpCode);
        $record['attempts']            = 0;
        $record['expires_at']          = $expiresAt->timestamp;
        $record['resend_available_at'] = now()->addSeconds(60)->timestamp;

        Cache::put($cacheKey, $record, $expiresAt);

        $delivery = $this->dispatchGuestOtpEmail($record['email'], $record['full_name'], $event, $otpCode);

        return view('events.qr_guest_checkin', $this->buildVenueCheckInPayload($event, $gate, [
            'activeTab'  => 'guest',
            'pendingOtp' => [
                'token'               => $token,
                'full_name'           => $record['full_name'],
                'masked_email'        => $this->maskEmail($record['email']),
                'expires_at_iso'      => $expiresAt->toIso8601String(),
                'resend_wait_seconds' => 60,
                'delivered_via_smtp'  => $delivery['sent'],
                'fallback_notice'     => $delivery['fallback_notice'],
                'demo_otp'            => $delivery['demo_otp'],
            ],
            'scanNotice' => $delivery['sent']
                ? 'A new 6-digit verification code was sent to ' . $this->maskEmail($record['email']) . '.'
                : null,
        ]));
    }

    public function show(Request $request, Registration $registration): View
    {
        $user = $request->user();

        if ($registration->user_id !== $user->id && ! $user->isHrCoordinator()) {
            abort(403, 'Unauthorized access to this registration check-in record.');
        }

        $registration->load(['event', 'user', 'waiver', 'checkIn']);

        return view('checkin.show', [
            'registration' => $registration,
            'event'        => $registration->event,
            'attendee'     => $registration->user,
        ]);
    }

    public function confirm(Request $request, Registration $registration): RedirectResponse
    {
        $currentUser = $request->user();

        if ($registration->user_id !== $currentUser->id && ! $currentUser->isHrCoordinator()) {
            abort(403, 'Unauthorized check-in attempt.');
        }

        try {
            $confirmed = $this->checkInService->confirmRegistrationCheckIn($registration, 'qr_scan', $currentUser);

            return back()->with('confirmed', $confirmed);
        } catch (RuntimeException $e) {
            return back()->withErrors(['waiver' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Unexpected failure confirming registration check-in', [
                'registration_id' => $registration->id,
                'actor_id'        => $currentUser->id,
                'error'           => $e->getMessage(),
            ]);

            return back()->withErrors([
                'checkin' => 'An error occurred while confirming check-in. Please contact the People & Culture desk.',
            ]);
        }
    }

    public function manual(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isHrCoordinator(), 403, 'Only HR Coordinators can perform manual Staff ID check-ins.');

        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'staff_id' => ['required', 'string', 'max:50'],
        ]);

        $event             = Event::findOrFail((int) $data['event_id']);
        $normalizedStaffId = strtolower(trim($data['staff_id']));
        $attendee          = User::whereRaw('LOWER(staff_id) = ?', [$normalizedStaffId])->first();

        if (! $attendee) {
            return back()->withInput()->withErrors([
                'staff_id' => 'Staff ID not found in the Strathmore University directory. Please verify and try again.',
            ]);
        }

        $registration = Registration::where('user_id', $attendee->id)
            ->where('event_id', $event->id)
            ->first();

        if (! $registration) {
            return back()->withInput()->withErrors([
                'staff_id' => 'No active registration found for this Staff ID for ' . $event->name . '.',
            ]);
        }

        try {
            $confirmed = $this->checkInService->confirmRegistrationCheckIn($registration, 'manual', $request->user());

            return back()->with('confirmed', $confirmed);
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['staff_id' => $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Unexpected failure during manual HR check-in', [
                'staff_id' => $normalizedStaffId,
                'event_id' => $event->id,
                'actor_id' => $request->user()->id,
                'error'    => $e->getMessage(),
            ]);

            return back()->withInput()->withErrors([
                'staff_id' => 'System error during manual check-in. Please assist at the People & Culture desk.',
            ]);
        }
    }

    public function ticket(Request $request, Registration $registration): View
    {
        $user = $request->user();

        if ($registration->user_id !== $user->id && ! $user->isHrCoordinator()) {
            abort(403, 'Unauthorized access to this event pass.');
        }

        $registration->load(['event', 'user', 'waiver', 'checkIn', 'feedback']);
        $gate = $this->checkInService->evaluateGateStatus($registration->event);

        return view('checkin.ticket', [
            'registration' => $registration,
            'event'        => $registration->event,
            'attendee'     => $registration->user,
            'gate'         => $gate,
        ]);
    }

    private function evaluateGuestWindow(array $gate): array
    {
        $tz          = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $now         = now($tz);
        $opensAt     = $gate['opens_at'];
        $sessionEnds = $gate['starts_at']->copy()->addHours(2);

        if ($now->lt($opensAt)) {
            return [
                'is_open' => false,
                'status'  => 'locked',
                'ends_at' => $sessionEnds,
                'message' => 'Visitor check-in opens at ' . $opensAt->format('h:i A') . ' EAT (1 hour before session start).',
            ];
        }

        if ($now->gt($sessionEnds)) {
            return [
                'is_open' => false,
                'status'  => 'closed',
                'ends_at' => $sessionEnds,
                'message' => 'This wellness session has already ended (' . $sessionEnds->format('h:i A') . ' EAT).',
            ];
        }

        return [
            'is_open' => true,
            'status'  => 'open',
            'ends_at' => $sessionEnds,
            'message' => null,
        ];
    }

    private function buildVenueCheckInPayload(Event $event, array $gate, array $overrides = []): array
    {
        $guestWindow = $this->evaluateGuestWindow($gate);

        return array_merge([
            'event'           => $event,
            'gate'            => $gate,
            'guestWindow'     => $guestWindow,
            'verifyUrl'       => URL::signedRoute('checkin.scan.verify', ['event' => $event->id]),
            'guestCheckInUrl' => URL::signedRoute('checkin.scan.guest', ['event' => $event->id]),
            'guestOtpUrl'     => URL::signedRoute('checkin.scan.guest.verify', ['event' => $event->id]),
            'guestResendUrl'  => URL::signedRoute('checkin.scan.guest.resend', ['event' => $event->id]),
            'activeTab'       => 'staff',
            'outcome'         => null,
            'guestOutcome'    => null,
            'pendingOtp'      => null,
            'attendee'        => null,
            'scanError'       => null,
            'scanNotice'      => null,
        ], $overrides);
    }

    private function guestOtpCacheKey(int $eventId, string $token): string
    {
        return "wellloop_guest_otp:{$eventId}:{$token}";
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        $visible          = mb_substr($local, 0, min(2, mb_strlen($local)));

        return $visible . str_repeat('*', max(2, mb_strlen($local) - 2)) . '@' . $domain;
    }

    /**
     * Dispatches the 6-digit OTP via configured Laravel Mail (SMTP).
     * If running locally with MAIL_MAILER=log/array or SMTP fails in dev mode, degrades gracefully without crashing.
     */
    private function dispatchGuestOtpEmail(string $email, string $fullName, Event $event, string $otpCode): array
    {
        $mailer  = (string) config('mail.default', 'log');
        $body    = "Hello {$fullName},\n\n"
            . "Your Strathmore University WellLoop visitor check-in verification code for \"{$event->name}\" is:\n\n"
            . "    {$otpCode}\n\n"
            . "This code expires in 10 minutes. Do not share it with anyone.\n\n"
            . "If you did not scan the venue QR code at {$event->location}, you may safely ignore this email.\n\n"
            . "— Strathmore University People & Culture (WPMIS)";

        if (in_array($mailer, ['log', 'array'], true)) {
            Log::info('WellLoop Guest OTP (local mail driver)', [
                'event_id' => $event->id,
                'email'    => $email,
                'otp'      => $otpCode,
            ]);

            return [
                'sent'            => false,
                'fallback_notice' => 'Local mail mode (MAIL_MAILER=' . $mailer . ') is active — configure SMTP in .env to deliver real emails to visitor inboxes.',
                'demo_otp'        => $otpCode,
            ];
        }

        try {
            Mail::raw($body, function ($message) use ($email, $fullName, $event): void {
                $message->to($email, $fullName)
                    ->subject("Strathmore WellLoop Verification Code — {$event->name}");
            });

            return [
                'sent'            => true,
                'fallback_notice' => null,
                'demo_otp'        => null,
            ];
        } catch (\Throwable $e) {
            Log::error('Failed to send Guest OTP email via SMTP', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return [
                'sent'            => false,
                'fallback_notice' => 'Email delivery could not reach SMTP right now. For testing/venue backup, your code is shown below.',
                'demo_otp'        => config('app.debug') ? $otpCode : null,
            ];
        }
    }
}