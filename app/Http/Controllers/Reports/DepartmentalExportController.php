<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DataProcessingLog;
use App\Services\DepartmentalEngagementService;
use Illuminate\Http\Response;

class DepartmentalExportController extends Controller
{
    public function __invoke(DepartmentalEngagementService $service): Response
    {
        DataProcessingLog::record('departmental_export');
        $ranking = $service->ranking();

        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, ['Department', 'Code', 'Total Staff', 'Unique Attendees', 'Engagement Rate (%)', 'Status']);

        foreach ($ranking['all_departments'] as $row) {
            fputcsv($stream, [
                $row['department'],
                $row['code'],
                (int) $row['total_staff'],
                (int) $row['unique_attendees'],
                number_format((float) $row['engagement_rate'], 1),
                ucfirst((string) $row['status']),
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream) ?: '';
        fclose($stream);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="Strathmore-Departmental-Engagement-' . date('Y-m-d') . '.csv"',
        ]);
    }
}