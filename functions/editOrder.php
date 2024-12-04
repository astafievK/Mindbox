<?php

require 'vendor/autoload.php';

use Ramsey\Uuid\Uuid;

include "src/settings.php";
include "src/headersJSON.php";

function editOrder($userId, $orderId, $externalOrderId){
    global $headers;
    global $endpointId;

    try {
        $transactionId = Uuid::uuid4()->toString();
    } catch (Exception $e) {
        echo "Ошибка создания UUID: " . $e->getMessage();
    }

    $url = "https://api.mindbox.ru/v3/operations/async?endpointId=$endpointId&operation=editOrder&transactionId=$transactionId";

    $data = [
        "customer" => [
            "ids" => [
                "mindboxId" => $userId
            ]
        ],
        "order" => [
            "ids" => [
                "mindboxId" => $orderId,
                "externalOrderId" =>  $externalOrderId
            ],
            "lines" => [
                [
                    "discountedPriceOfLine" => "3750000",
                    "quantity" => "1",
                    "lineNumber" => "1",
                    "status" => "OrderTraffic",
                    "product" => [
                        "ids" => [
                            "c1" => "1488"
                        ]
                    ]
                ]
            ]
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_CAINFO, 'C:/Certificates/cacert.pem');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Ошибка запроса: " . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}