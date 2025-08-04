<?php
require __DIR__ . '/vendor/autoload.php';

use Twilio\Rest\Client;

$sid = "YOUR_TWILIO_ACCOUNT_SID";
$token = "YOUR_TWILIO_AUTH_TOKEN";
$twilioNumber = "YOUR_TWILIO_NUMBER";

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