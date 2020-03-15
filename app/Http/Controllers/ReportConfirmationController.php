<?php

namespace App\Http\Controllers;

use App\Report;
use App\ReportConfirmation;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportConfirmationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['store', 'destroy']);
    }

    public function store($id)
    {
        $report = Report::findOrFail($id);

        if(auth()->user()->id == $report->reporter_id){
            return response()->json(null, Response::HTTP_NOT_ACCEPTABLE);
        }
        
        if ($report->confirmBy(auth()->user())) {
            return response()->json(null, Response::HTTP_CREATED);
        }
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        $confirmation = ReportConfirmation::where('reporter_id', auth()->user()->id)->where('report_id', $id)->first();
        if($confirmation){
            $confirmation->delete();
        }

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
