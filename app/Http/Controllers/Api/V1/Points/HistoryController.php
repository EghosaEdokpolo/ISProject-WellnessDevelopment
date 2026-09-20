<?php

namespace App\Http\Controllers\Api\V1\Points;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __invoke(Request $request, Staff $staff)
    {
        return response()->json([
            'staff_id'        => $staff->staff_id_number,
            'name'            => $staff->fullName(),
            'points_balance'  => $staff->points_balance,
            'transactions'    => $staff->pointsTransactions()->latest()->limit(100)->get(),
        ]);
    }
}