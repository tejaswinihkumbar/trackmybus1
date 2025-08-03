<?php
require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;

$sid = 'AC2b732ee91f93cf6f53fde036cbdb3df8';
$token = 'b6035f319b955168605fe690c72bb5ee';
$twilioNumber = '+17163335712';

$client = new Client($sid, $token);

function sendNotification($toPhone, $message) {
    global $client, $twilioNumber;

    try {
        $client->messages->create(
            $toPhone,
            [
                'from' => $twilioNumber,
                'body' => $message
            ]
        );
    } catch (Exception $e) {
        error_log("Twilio Error: " . $e->getMessage());
    }
}
?>