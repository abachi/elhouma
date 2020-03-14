<?php

namespace App\Http\Controllers;

use Storage;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportsPicturesController extends Controller
{
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'picture' => 'required|image',
        ]);
        
        $report = auth()->user()->findReport($id);

        if (!$report) {
            return response()->json(['error' => __('There is no report with this id.')], Response::HTTP_NOT_FOUND);
        }

        $newPicture = $request->picture->store('images', 'public');
        $oldPicture = $report->picture;
        $report->picture = $newPicture;
        $report->save();

        Storage::disk('public')->delete($oldPicture);

        return response()->json([
            'report' => $report
        ], Response::HTTP_ACCEPTED);
    }

}
