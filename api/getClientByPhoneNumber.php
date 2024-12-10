<?php

// Получение пользователя по номеру телефона

include "config/settings.php";
include "config/headersJSON.php";

function getClientByPhoneNumber($mobilePhone){
    global $headers;
    global $endpointId;

    $url = "https://api.mindbox.ru/v3/operations/sync?endpointId=$endpointId&operation=GetUserByPhoneNumber";

    $data = [
        "customer" => [
            "mobilePhone" => $mobilePhone
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

    $result = json_decode($response, true);
    
    if(isset($result['customer'])){
        switch ($result['customer']['processingStatus']) {
            case 'Found':
                return true;
                break;
            case 'NotFound':
                return false;
                break;
            case 'Ambiguous':
                echo "Найдено более одного клиента по переданным идентификаторам.\n";
                return true;
                break;
            default:
                echo "Неизвестный статус обработки клиента: $result\n";
                return true;
                break;
        }
    }

    return false;
}