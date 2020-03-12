<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IssueConfirmation extends Model
{
    protected $fillable = ['reporter_id', 'report_id'];
}
