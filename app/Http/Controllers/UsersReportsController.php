<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Report as ReportResource;

class UsersReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['index', 'destroy']);
    }

    public function index()
    {
        return ReportResource::collection(auth()->user()->reports);
    }

    public function destroy($id)
    {
        $report = auth()->user()->findReport($id);

        if(!$report){
            return response()->json(['error' => __('There is no report with this id.')], 404);
        }
        
        $report->delete();
        return response()->json(null, 204);
    }
}
