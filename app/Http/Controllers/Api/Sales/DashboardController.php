<?php

namespace App\Http\Controllers\Api\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Helpers\ApiResponse;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //

    public function dashboard(Request $request)
    {
        try {

            /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */
            $year  = $request->get('year', Carbon::now()->year);
            $month = $request->get('month'); // optional (1–12 or null)

            /*
        |--------------------------------------------------------------------------
        | Base Queries
        |--------------------------------------------------------------------------
        */
            $earningsQuery = Lead::selectRaw("
            MONTH(created_at) as month,
            SUM(amount) as total
        ")->whereYear('created_at', $year);

            $qualifiedQuery = Lead::selectRaw("
            MONTH(created_at) as month,
            COUNT(*) as total
        ")
                ->where('qualified', true)
                ->whereYear('created_at', $year);

            $totalLeadsQuery = Lead::selectRaw("
            MONTH(created_at) as month,
            COUNT(*) as total
        ")->whereYear('created_at', $year);

            /*
        |--------------------------------------------------------------------------
        | Optional Month Filter
        |--------------------------------------------------------------------------
        */
            if (!empty($month)) {
                $earningsQuery->whereMonth('created_at', $month);
                $qualifiedQuery->whereMonth('created_at', $month);
                $totalLeadsQuery->whereMonth('created_at', $month);
            }

            /*
        |--------------------------------------------------------------------------
        | Execute Queries
        |--------------------------------------------------------------------------
        */
            $earnings = $earningsQuery
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $qualifiedLeads = $qualifiedQuery
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $totalLeads = $totalLeadsQuery
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            /*
        |--------------------------------------------------------------------------
        | Format to 12-month arrays (important for charts)
        |--------------------------------------------------------------------------
        */
            $monthlyEarnings = array_fill(0, 12, 0);
            $qualifiedData   = array_fill(0, 12, 0);
            $totalLeadsData  = array_fill(0, 12, 0);

            foreach ($earnings as $item) {
                $monthlyEarnings[$item->month - 1] = (float) $item->total;
            }

            foreach ($qualifiedLeads as $item) {
                $qualifiedData[$item->month - 1] = (int) $item->total;
            }

            foreach ($totalLeads as $item) {
                $totalLeadsData[$item->month - 1] = (int) $item->total;
            }

            /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */
            return ApiResponse::success([
                'monthly_earnings' => $monthlyEarnings,
                'qualified_leads'  => $qualifiedData,
                'monthly_leads'    => $totalLeadsData,
                'yearly_leads'     => Lead::whereYear('created_at', $year)->count(),
            ], 'Dashboard data fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                $e->getMessage(),
                500
            );
        }
    }
}
