<?php

namespace Tests\Feature;

use App\FacebookInterface;
use Facebook\Exceptions\FacebookResponseException;

class FakeFacebook implements FacebookInterface {

    public function get($accessToken)
    {
        
        try {
            if($accessToken === 'valid_facebook_access_token'){
                $response = true;
            }else{
                throw new \Exception;
            }
        } catch (\Exception $e) {
            return null;
        }
        return $response;
    }
}