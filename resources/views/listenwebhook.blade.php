<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

\Stripe\Stripe::setApiKey("sk_test_KCwU7jVLIoQtECJsXAEXJg1q");

// Retrieve the request's body and parse it as JSON
$input = @file_get_contents("php://input");
$event_json = json_decode($input);



http_response_code(200);