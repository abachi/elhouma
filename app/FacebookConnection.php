<?php


namespace App;

use \Facebook\Facebook;
use App\FacebookInterface; // this would be social login interface so we can use another login methods ex: google


class FacebookConnection implements FacebookInterface{

    private $facebook;

    public function __construct($config)
    {
        $this->facebook =  new \Facebook\Facebook($config); 
    }

    public function get($accessToken)
    {
        // this should get a user or return null when the something went wrong
        try {
            $response = $this->facebook->get('/me', $accessToken);
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            return null;
        }
        return $response;
    }
}