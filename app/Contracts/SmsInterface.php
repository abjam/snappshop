<?php

interface SmsInterface {

    public function __construct($url, $message, $receptor, $sender);
    public function sendSms();
}
