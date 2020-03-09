<?php

namespace App\Http\Controllers;

use App\Report;
use App\Http\Resources\Report as ReportResource;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index(){
        return ReportResource::collection(Report::all());
    }
}
