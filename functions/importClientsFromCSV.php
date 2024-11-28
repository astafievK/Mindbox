<?php

// Массовый импорт клиентов из XML

include "src/settings.php";
include "src/headersJSON.php";

function importClientsFromCSV($filePath){
    global $endpointId;

    $csvCodePage = "65001";
    $csvColumnDelimiter = '%2C';
    $csvTextQualifier = '%22';
    $segment = "TestovyjSegment"; // Системное имя сегмента для клиентов
    $sourceActionTemplate = "Registration"; // Действие регистрации клиента
    $transactionId = uniqid(); // Заключение ключа идемпотентности в формате GUID

    $url = "https://api.mindbox.ru/v3/operations/bulk?endpointId=$endpointId&operation=ImportClientsFromCSV&csvCodePage=$csvCodePage&csvColumnDelimiter=$csvColumnDelimiter&csvTextQualifier=$csvTextQualifier&segment=$segment&SourceActionTemplate=$sourceActionTemplate&transactionId=$transactionId";

    echo $url;

    if (!file_exists($filePath)) {
        throw new Exception("Файл $filePath не найден");
    }

    $headers = [
        "Content-Type: multipart/form-data; boundary=$transactionId",
        "Accept: application/json"
    ];

    $fileContent = file_get_contents($filePath);
    $fileContent = mb_convert_encoding($fileContent, "UTF-8", "Windows-1251");

    $body = "--$transactionId\r\n";
    $body .= "Content-Disposition: form-data; name=\"file\"; filename=\"" . basename($filePath) . "\"\r\n";
    $body .= "Content-Type: text/csv\r\n\r\n";
    $body .= $fileContent . "\r\n";
    $body .= "--$transactionId--\r\n";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_CAINFO, 'C:/Certificates/cacert.pem');
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Ошибка запроса: " . curl_error($ch));
    }

    curl_close($ch);

    return $response;
}


