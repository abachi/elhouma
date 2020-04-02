<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class SocialUser extends User
{
    protected $table = 'social_users';
    protected $fillable = [
        'name',
        'email',
        'provider',
        'provider_token',
        'provider_user_id',
    ];
}
