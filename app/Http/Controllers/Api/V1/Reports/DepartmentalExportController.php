<?php

namespace App\Http\Controllers\Api\V1\Reports;

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

        $csv = "Department,Code,Total Staff,Unique Attendees,Engagement Rate,Status\n";
        foreach ($ranking['all_departments'] as $row) {
            $csv .= sprintf(
                "%s,%s,%d,%d,%.1f,%s\n",
                $row['department'], $row['code'], $row['total_staff'],
                $row['unique_attendees'], $row['engagement_rate'], $row['status']
            );
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="departmental-engagement.csv"',
        ]);
    }
}