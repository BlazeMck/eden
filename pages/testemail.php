<?php
require '../vendor/autoload.php';
use \Mailjet\Resources;

// Use your saved credentials, specify that you are using Send API v3.1

$mj = new \Mailjet\Client('fe844b065c47c94b20e735bdccf319b9', 'eb9d8e6206273615ca513814c644731a',true,['version' => 'v3.1']);

// Define your request body

$body = [
    'Messages' => [
        [
            'From' => [
                'Email' => "blazemckinlay@gmail.com",
                'Name' => "Me"
            ],
            'To' => [
                [
                    'Email' => "blazemckinlay@gmail.com",
                    'Name' => "You"
                ]
            ],
            'Subject' => "My first Mailjet Email!",
            'TextPart' => "Greetings from Mailjet!",
            'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href=\"https://www.mailjet.com/\">Mailjet</a>!</h3>
            <br />May the delivery force be with you!"
        ]
    ]
];

// All resources are located in the Resources class

$response = $mj->post(Resources::$Email, ['body' => $body]);

// Read the response

$response->success() && var_dump($response->getData());

include('../includes/header.html');
include('../includes/footer.html');
?>