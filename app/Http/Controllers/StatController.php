<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function total()
    {
        $total = Report::all()->count();

        return response()->json([
            'total' => $total,
            'waiting' => Report::waiting()->count(),
            'confirmed' => Report::confirmed()->count(),
            'fixed' => Report::fixed()->count(),
        ], 200);
    }
}
