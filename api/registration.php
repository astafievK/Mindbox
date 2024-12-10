<?php

// Регистрация пользователя

include "config/settings.php";
include "config/headersJSON.php";

function registration(
    $phoneNumber,
    $email,
    $fullName
): string
{
    global $headers;
    global $endpointId;

    $url = "https://api.mindbox.ru/v3/operations/sync?endpointId=$endpointId&operation=Registration";

    $data = [
        "customer" => [
            "mobilePhone" => $phoneNumber,
            "fullName" => $fullName,
            "email" => $email
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

    if (curl_errno($ch)) {
        throw new Exception("Ошибка запроса: " . curl_error($ch));
    }

    curl_close($ch);

    if (is_string($response)) {
        $response = json_decode($response, true); // Преобразуем строку в ассоциативный массив
    }
    
    if ($response['status'] == 'ValidationError') {
        $errorMessage = isset($response['validationMessages'][0]['message']) ? $response['validationMessages'][0]['message'] : 'Неизвестная ошибка';
        return "Ошибка добавления [$fullName]: $errorMessage";
    } else {
        return "Пользователь $fullName ($phoneNumber) зарег-ан";
    }
}