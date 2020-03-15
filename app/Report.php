<?php

namespace App;

use App\User;
use App\ReportFix;
use App\ReportConfirmation;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];

    public function isConfirmedBy(User $user)
    {
        return ReportConfirmation::where('report_id', $this->id)
            ->where('reporter_id', $user->id)
            ->first() != null;
    }

    public function confirmBy(User $user)
    {
        if(! $this->isConfirmedBy($user)){
            ReportConfirmation::create([
                'report_id' => $this->id,
                'reporter_id' => $user->id,
            ]);
        }
        return true;
    }

    public function isFixedBy(User $user)
    {
        return ReportFix::where('report_id', $this->id)
            ->where('user_id', $user->id)
            ->first() != null;
    }

    public function fixedRequestBy(User $user)
    {
        if(! $this->isFixedBy($user)){
            ReportFix::create([
                'report_id' => $this->id,
                'user_id' => $user->id,
            ]);
        }
        return true;
    }
}
