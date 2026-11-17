<?php

declare(strict_types=1);

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DataProcessingLog;
use App\Services\DepartmentalEngagementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PdfExportController extends Controller
{
    public function __invoke(DepartmentalEngagementService $service): Response
    {
        DataProcessingLog::record('pdf_report_exported');
        $ranking = $service->ranking();

        $pdf = Pdf::loadView('reports.pdf', compact('ranking'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'isRemoteEnabled'      => true,
                'isHtml5ParserEnabled' => true,
            ]);

        return $pdf->download('Strathmore-WPMIS-Departmental-Report-' . date('Y-m-d') . '.pdf');
    }
}