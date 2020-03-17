<?php

namespace App\Http\Controllers;

use App\Report;
Use App\Http\Resources\Report as ReportResources;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function confirmed()
    {
        $reports = Report::confirmed()->get();
        return ReportResources::collection($reports);
    }

    public function fixed()
    {
        $reports = Report::fixed()->get();
        return ReportResources::collection($reports);
    }

    public function waiting()
    {
        $reports = Report::waiting()->get();
        return ReportResources::collection($reports);
    }
}
