<?php

namespace App;

use App\User;
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
}
