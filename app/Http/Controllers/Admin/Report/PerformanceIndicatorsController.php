<?php

namespace App\Http\Controllers\Admin\Report;

use Carbon\Carbon;
use App\Http\Controllers\Controller;

class PerformanceIndicatorsController extends Controller
{

    /**
     * Get the performance indicators for the application.
     *
     * @return Response
     */
    public function all()
    {
        return view('admin.report.platform.kpi');
    }

    /**
     * Get the revenue amounts for the application.
     *
     * @return Response
     */
    public function revenue()
    {
        return [
            'monthlyRecurringRevenue' => $this->indicators->monthlyRecurringRevenue(),
            'totalVolume' => $this->indicators->totalVolume(),
        ];
    }
}
