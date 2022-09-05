<?php

namespace App\Contracts\SendSms;

use SmsInterface;

class SendSms implements SmsInterface
{
    private $url;
    private $message;
    private $receptor;
    private $sender;

    public function __construct($url, $message, $receptor, $sender)
    {
        $this->url = $url;
        $this->message = $message;
        $this->receptor = $receptor;
        $this->sender = $sender;
    }

    public function sendSms()
    {
        $postFields = "message=".$this->message.
                      "&sender=".$this->sender.
                      "&receptor=".$this->receptor;

        //Initiate cURL.
        $ch = curl_init($this->url);

        //Tell cURL that we want to send a POST request.
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        //Execute the request
        $result = curl_exec($ch);
        $response = json_decode($result, true);

        return $response;
    }
}
