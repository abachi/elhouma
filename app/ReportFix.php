<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportFix extends Model
{
    protected $table = 'fixed_issues';
    protected $fillable = ['report_id', 'user_id'];
}
