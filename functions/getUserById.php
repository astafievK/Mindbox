<?php

// Получение пользователя по Mindbox Id

include "src/settings.php";
include "src/headersJSON.php";

function getUserById($userId){
    global $headers;
    global $endpointId;

    $url = "https://api.mindbox.ru/v3/operations/sync?endpointId=$endpointId&operation=GetUserByMindboxId";

    $data = [
        "customer" => [
            "ids" => [
                "mindboxId" => $userId
            ],
        ],
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
    curl_close($ch);

    return $response;
}