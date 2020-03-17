<?php

namespace App\Http\Controllers;

use Storage;

use App\Report;
use App\IssueConfirmation;
use Illuminate\Http\Request;
use App\Http\Requests\StoreReport;
use App\Http\Requests\IssueFixedRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\IssueConfirmationRequest;
use App\Http\Resources\Report as ReportResource;

class ReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only(['store', 'confirm', 'update']);
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
            'confirmed' => false,
            'fixed' => false,
        ]);

        if ($report) {
            return response()->json([
                'report' => $report
            ], Response::HTTP_CREATED);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required',
            'fixed' => 'required',
            'confirmed' => 'required',
        ]);
        
        $report = auth()->user()->findReport($id);

        if (!$report) {
            return response()->json(['error' => __('There is no report with this id.')], Response::HTTP_NOT_FOUND);
        }

        $report->description = (string) $request->description;
        $report->confirmed = $request->confirmed;
        $report->fixed = $request->fixed;
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

    public function myReports()
    {
        return ReportResource::collection(auth()->user()->reports);
    }
}
