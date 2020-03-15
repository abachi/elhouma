<?php

namespace App\Http\Controllers;

use App\Report;
use App\ReportFix;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportFixController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['store', 'destroy']);
    }
    
    public function store($id)
    {
        $report = Report::findOrFail($id);
        
        if ($report->fixedRequestBy(auth()->user())) {
            return response()->json(null, Response::HTTP_CREATED);
        }
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        $fix = ReportFix::where('report_id', $id)->where('user_id', auth()->user()->id)->first();

        if($fix){
            $fix->delete();
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);

    }
}
