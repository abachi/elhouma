<?php

namespace App\Http\Controllers;

use App\Report;
use App\Http\Resources\Report as ReportResource;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReport;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store']]);
    }

    public function index()
    {
        return ReportResource::collection(Report::all());
    }

    public function store(StoreReport $request)
    {   
        $request->validated();
        $picture = $request->picture->store('images', 'public');
        
        $report = Report::create([
            'reporter_id' => auth()->user()->id,
            'description' => $request->description,
            'lat' => $request->lat,
            'lng' => $request->lng,
            'picture' => $picture,
        ]);

        if ($report) {
            return response()->json([
                'report' => $report
            ], 201);
        }
    }
}
