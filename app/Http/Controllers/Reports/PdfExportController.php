<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DataProcessingLog;
use App\Services\DepartmentalEngagementService;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfExportController extends Controller
{
    public function __invoke(DepartmentalEngagementService $service)
    {
        DataProcessingLog::record('pdf_report_exported');
        $ranking = $service->ranking();

        $pdf = Pdf::loadView('reports.pdf', compact('ranking'));
        
        return $pdf->download('WPMIS-Departmental-Report-' . date('Y-m-d') . '.pdf');
    }
}