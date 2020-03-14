<?php

namespace App;

use App\User;
use App\FixedIssue;
use App\IssueConfirmation;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $guarded = [];

    public function isConfirmedBy(User $user)
    {
        return IssueConfirmation::where('report_id', $this->id)
            ->where('reporter_id', $user->id)
            ->first() != null;
    }

    public function confirmBy(User $user)
    {
        if(! $this->isConfirmedBy($user)){
            IssueConfirmation::create([
                'report_id' => $this->id,
                'reporter_id' => $user->id,
            ]);
        }
        return true;
    }

    public function isFixedBy(User $user)
    {
        return FixedIssue::where('report_id', $this->id)
            ->where('user_id', $user->id)
            ->first() != null;
    }

    public function fixedBy(User $user)
    {
        if(! $this->isFixedBy($user)){
            FixedIssue::create([
                'report_id' => $this->id,
                'user_id' => $user->id,
            ]);
        }
        return true;
    }
}
