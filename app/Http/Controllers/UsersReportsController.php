<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Report as ReportResource;

class UsersReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->only('index');
    }

    public function index()
    {
        return ReportResource::collection(auth()->user()->reports);
    }
}
