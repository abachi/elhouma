<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportFix extends Model
{
    protected $table = 'fixed_issue_requests';
    protected $fillable = ['report_id', 'user_id'];
}
