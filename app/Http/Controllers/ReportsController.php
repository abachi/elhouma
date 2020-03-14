<?php

namespace App\Http\Controllers;

use Storage;

use App\Report;
use App\IssueConfirmation;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReport;
use App\Http\Requests\UpdateReport;
use App\Http\Requests\IssueFixedRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\IssueConfirmationRequest;
use App\Http\Resources\Report as ReportResource;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'only' => [
                'store', 'confirm',
                'updateDescription', 'updatePosition'
        ]]);
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
            ], Response::HTTP_CREATED);
        }
    }

    public function updatePosition(Request $request)
    {
        $request->validate([
            'report_id' => 'required|numeric',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);
        
        $report = auth()->user()->findReport($request->report_id);

        if (!$report) {
            return response()->json(['error' => __('There is no report with this id.')], Response::HTTP_NOT_FOUND);
        }

        $report->lat = $request->lat;
        $report->lng = $request->lng;
        $report->save();

        return response()->json([
            'report' => $report
        ], Response::HTTP_ACCEPTED);
    }

    public function updateDescription(Request $request)
    {
        $request->validate([
            'report_id' => 'required|numeric',
            'description' => 'required'
        ]);
        
        $report = auth()->user()->findReport($request->report_id);

        if (!$report) {
            return response()->json(['error' => __('There is no report with this id.')], Response::HTTP_NOT_FOUND);
        }

        $report->description = (string) $request->description;
        $report->save();

        return response()->json([
            'report' => $report
        ], Response::HTTP_ACCEPTED);
    }

    public function confirm(IssueConfirmationRequest $request)
    {
        $request->validated();

        $report = Report::findOrFail($request->report_id);

        if ($report->confirmBy(auth()->user())) {
            return response()->json([
                'report' => $report,
                'status' => 'confirmed',
            ], Response::HTTP_CREATED);
        }
    }

    public function fixed(IssueFixedRequest $request)
    {
        $request->validated();

        $report = Report::findOrFail($request->report_id);

        if ($report->fixedBy(auth()->user())) {
            return response()->json(null, Response::HTTP_CREATED);
        }
    }

    public function myReports()
    {
        return ReportResource::collection(auth()->user()->reports);
    }
}
