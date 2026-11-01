<?php

namespace App\Http\Controllers;

use App\Services\WellnessImpactService;
use Illuminate\Http\Request;
use App\Models\Event;

class HrImpactController extends Controller
{
    public function index(Request $request, WellnessImpactService $service)
    {
        $category = $request->query('category');
        if ($category === 'all' || !$category) {
            $category = null;
        }

        // 1. Chart & General Table Timeline Data (Structured by 12 Months)
        $monthlyChartData = $service->categoryBreakdown($category);
        $eventTableRows = $service->eventTable($category);

        // 2. Separate Dimension-Specific Calculation for the Top Banner High/Low 
        // We calculate this using a dedicated loop 
        $highestDim = 'None';
        $lowestDim = 'None';

        if ($category === null) {
            $dimensionsList = ['physical', 'mental', 'financial', 'social', 'intellectual', 'occupational', 'environmental', 'spiritual'];
            $dimensionLifts = [];

            foreach ($dimensionsList as $dim) {
                // Fetch events specifically matching this dimension type
                $eventIds = \App\Models\Event::where('category', $dim)->pluck('id');
                $regIds = \App\Models\Registration::whereIn('event_id', $eventIds)->pluck('id');
                
                if ($regIds->isEmpty()) {
                    $dimensionLifts[$dim] = 0;
                    continue;
                }

                $preAvg = \App\Models\WellbeingResponse::whereIn('registration_id', $regIds)->where('stage', 'pre')->avg('total_score') ?? 0;
                $postAvg = \App\Models\WellbeingResponse::whereIn('registration_id', $regIds)->where('stage', 'post')->avg('total_score') ?? 0;

                $dimensionLifts[$dim] = round($postAvg - $preAvg, 1);
            }

            // Sort the dimension metrics map cleanly down from highest to lowest lift values
            $sortedDimensions = collect($dimensionLifts)->sortByDesc(fn($v) => $v);
            $highestDim = $sortedDimensions->keys()->first() ?? 'None';
            $lowestDim = $sortedDimensions->keys()->last() ?? 'None';
        }

        // 3. Compute Aggregated Summary Numbers
        $totalEvents = count($eventTableRows);
        $avgLiftGlobal = $totalEvents ? collect($monthlyChartData)->avg('avg_lift') : 0;
        $avgPreGlobal  = $totalEvents ? collect($monthlyChartData)->avg('pre_avg') : 0;
        $avgPostGlobal = $totalEvents ? collect($monthlyChartData)->avg('post_avg') : 0;

        // AJAX background engine response channel configuration mapping
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'avgLift'        => ($avgLiftGlobal > 0 ? '+' : '') . round($avgLiftGlobal, 1),
                'avgPreScore'    => round($avgPreGlobal, 1),
                'avgPostScore'   => round($avgPostGlobal, 1),
                'eventsInView'   => $totalEvents,
                'highestDim'     => $highestDim,
                'lowestDim'      => $lowestDim,
                'graphBars'      => $monthlyChartData, // Feeds 12 calendar months to JS Chart
                'eventsTableRows'=> $eventTableRows
            ]);
        }

        // Primary initial layout view delivery target mapping matrix variables
        return view('hrview.hrdb_impact', [
            'categories'     => $monthlyChartData, // Feeds 12 calendar months to Blade Chart loop
            'eventTable'     => $eventTableRows,
            'activeCategory' => $category,
            'highestDim'     => $highestDim,
            'lowestDim'      => $lowestDim,
            'avgLiftGlobal'  => $avgLiftGlobal,
            'avgPreGlobal'   => $avgPreGlobal,
            'avgPostGlobal'  => $avgPostGlobal
        ]);
    }
}
