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

    public function dashboard()
    {
        try {

            /*
        |--------------------------------------------------------------------------
        | Initialize Monthly Arrays
        |--------------------------------------------------------------------------
        */

            $monthlyEarnings = array_fill(0, 12, 0);

            $qualifiedLeads = array_fill(0, 12, 0);
            $totalLeads = array_fill(0, 12, 0);


            /*
        |--------------------------------------------------------------------------
        | Monthly Earnings
        |--------------------------------------------------------------------------
        */

            $earnings = Lead::selectRaw("
                MONTH(created_at) as month,
                SUM(amount) as total
            ")
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($earnings as $earning) {

                $monthIndex = $earning->month - 1;

                $monthlyEarnings[$monthIndex] = (float) $earning->total;
            }

            /*
        |--------------------------------------------------------------------------
        | Qualified Leads Per Month
        |--------------------------------------------------------------------------
        */

            $leads = Lead::selectRaw("
                MONTH(created_at) as month,
                COUNT(*) as total
            ")
                ->where('qualified', true)
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($leads as $lead) {

                $monthIndex = $lead->month - 1;

                $qualifiedLeads[$monthIndex] = $lead->total;
            }

            /*
        |--------------------------------------------------------------------------
        | Total Leads This Year
        |--------------------------------------------------------------------------
        */

            $yearlyLeads = Lead::whereYear(
                'created_at',
                Carbon::now()->year
            )
                ->count();

            $leadsPerMonth = Lead::selectRaw("
        MONTH(created_at) as month,
        COUNT(*) as total
    ")
                ->whereYear('created_at', Carbon::now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            foreach ($leadsPerMonth as $item) {
                $monthIndex = $item->month - 1;
                $totalLeads[$monthIndex] = (int) $item->total;
            }

            /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

            return ApiResponse::success([
                'monthly_earnings' => $monthlyEarnings,
                'qualified_leads'  => $qualifiedLeads,
                'yearly_leads'     => $yearlyLeads,
                'monthly_leads'    => $totalLeads,
            ], 'Dashboard data fetched successfully');
        } catch (\Exception $e) {

            return ApiResponse::error(
                $e->getMessage(),
                500
            );
        }
    }
}
