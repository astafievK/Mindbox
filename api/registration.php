<?php

// Регистрация пользователя

include "config/settings.php";
include "config/headersJSON.php";

function registration(
    $email,
    $phoneNumber,
    $firstName = null,
    $lastName = null,
    $middleName = null,
    $password = null,
    ): string
{

    global $headers;
    global $endpointId;

    $url = "https://api.mindbox.ru/v3/operations/sync?endpointId=$endpointId&operation=Registration";

    $data = [
        "customer" => [
            "lastName" => $lastName,
            "firstName" => $firstName,
            "middleName" => $middleName,
            "mobilePhone" => $phoneNumber,
            "email" => $email,
            "password" => $password,
        ],
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_CAINFO, 'C:/Certificates/cacert.pem');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, true));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    curl_close($ch);
//    $responseData = json_decode($response, true);

//    if (isset($responseData['status'])) {
//        if ($responseData['status'] === 'ValidationError') {
//            if (!empty($responseData['validationMessages']) && isset($responseData['validationMessages'][0]['message'])) {
//                $returnValue = $responseData['validationMessages'][0]['message'];
//            } else {
//                $returnValue = $responseData;
//            }
//        } else {
//            $returnValue = "Учетная запись успешно создана";
//        }
//    } else {
//        $returnValue = "Ошибка: Ответ сервера не содержит статус.";
//    }

    return json_decode($response, true);
}