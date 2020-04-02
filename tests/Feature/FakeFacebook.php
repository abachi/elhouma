<?php

namespace Tests\Feature;

use App\ISocialLogin;

class FakeFacebook implements ISocialLogin {

    public function get($path)
    {
        return true;
    }
}