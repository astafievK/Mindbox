<?php

// Массовый импорт клиентов из XML

include "src/settings.php";
include "src/headersJSON.php";

function importClientsFromCSV($filePath){
    global $endpointId;

    $segment = "TestovyjSegment";
    $csvCodePage = "65001";
    $csvColumnDelimiter = '%3B';
    $csvTextQualifier = '';
    $sourceActionTemplate = "Registration"; // Действие регистрации клиента
    $transactionId = uniqid(); // Заключение ключа идемпотентности в формате GUID

    $url = "https://api.mindbox.ru/v3/operations/bulk?endpointId=$endpointId&segment=$segment&operation=ImportClientsFromCSV&csvCodePage=$csvCodePage&csvColumnDelimiter=$csvColumnDelimiter&csvTextQualifier=$csvTextQualifier&SourceActionTemplate=$sourceActionTemplate&transactionId=$transactionId";

    if (!file_exists($filePath)) {
        throw new Exception("Файл $filePath не найден");
    }

    $headers = [
        "Content-Type: multipart/form-data; boundary=$transactionId",
        "Accept: application/json"
    ];

    $file = curl_file_create($filePath, 'text/csv', $filePath);

    print_r($file);

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_CAINFO, 'C:/Certificates/cacert.pem');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "file" => $file
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Ошибка запроса: " . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}


