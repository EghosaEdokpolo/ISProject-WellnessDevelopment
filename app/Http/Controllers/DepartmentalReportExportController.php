<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DepartmentalReportExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        abort_unless($request->user()?->isHrCoordinator(), 403, 'Unauthorized access to departmental reports.');

        $tz  = (string) config('wellloop.timezone', 'Africa/Nairobi');
        $now = now($tz);

        // Align with academic year window (Sep 1 – Aug 31)
        $academicStartYear = $now->month >= 9 ? $now->year : $now->year - 1;
        $periodStart       = $now->copy()->setDate($academicStartYear, 9, 1)->startOfDay();
        $periodEnd         = $periodStart->copy()->addYear()->subSecond();

        $staffByDept = User::query()
            ->select('department', DB::raw('COUNT(*) as total_staff'))
            ->where('role', 'staff')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->groupBy('department')
            ->pluck('total_staff', 'department');

        $attendanceByDept = DB::table('registrations')
            ->join('users', 'registrations.user_id', '=', 'users.id')
            ->select(
                'users.department',
                DB::raw('COUNT(DISTINCT users.id) as unique_attendees'),
                DB::raw('COUNT(registrations.id) as total_sessions'),
                DB::raw("SUM(CASE WHEN registrations.status = 'completed' THEN 1 ELSE 0 END) as completed_sessions")
            )
            ->where('users.role', 'staff')
            ->whereNotNull('users.department')
            ->where('users.department', '!=', '')
            ->whereIn('registrations.status', ['checked_in', 'completed'])
            ->whereBetween('registrations.created_at', [
                $periodStart->toDateTimeString(),
                $periodEnd->toDateTimeString(),
            ])
            ->groupBy('users.department')
            ->get()
            ->keyBy('department');

        $rows = [];

        foreach ($staffByDept as $department => $totalStaff) {
            $totalStaffInt     = (int) $totalStaff;
            $metrics           = $attendanceByDept->get($department);
            $uniqueAttendees   = (int) ($metrics->unique_attendees ?? 0);
            $totalSessions     = (int) ($metrics->total_sessions ?? 0);
            $completedSessions = (int) ($metrics->completed_sessions ?? 0);
            $participationRate = $totalStaffInt > 0
                ? round(($uniqueAttendees / $totalStaffInt) * 100, 1)
                : 0.0;

            $rows[] = [
                'department'         => (string) $department,
                'total_staff'        => $totalStaffInt,
                'unique_attendees'   => $uniqueAttendees,
                'total_sessions'     => $totalSessions,
                'completed_sessions' => $completedSessions,
                'participation_rate' => $participationRate,
            ];
        }

        usort($rows, static function (array $a, array $b): int {
            return [$b['participation_rate'], $b['unique_attendees'], $a['department']]
                <=> [$a['participation_rate'], $a['unique_attendees'], $b['department']];
        });

        $filename = 'strathmore-wellloop-departmental-report-' . $now->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows, $periodStart, $periodEnd, $now): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            // UTF-8 BOM so Microsoft Excel opens characters cleanly
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Strathmore University WellLoop — Departmental Wellness Participation Report',
            ]);
            fputcsv($handle, [
                'Academic Window: ' . $periodStart->format('d M Y') . ' to ' . $periodEnd->format('d M Y'),
                'Generated At: ' . $now->format('d M Y H:i') . ' EAT',
            ]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'Rank',
                'Department',
                'Total Staff',
                'Unique Participating Staff',
                'Participation Rate (%)',
                'Total Verified Check-Ins',
                'Completed Sessions (With Feedback)',
            ]);

            $rank = 1;
            foreach ($rows as $row) {
                fputcsv($handle, [
                    $rank++,
                    $row['department'],
                    $row['total_staff'],
                    $row['unique_attendees'],
                    number_format($row['participation_rate'], 1) . '%',
                    $row['total_sessions'],
                    $row['completed_sessions'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}