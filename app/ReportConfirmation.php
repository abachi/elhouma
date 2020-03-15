<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportConfirmation extends Model
{
    protected $fillable = ['reporter_id', 'report_id'];
    protected $table = 'issue_confirmations';
}
