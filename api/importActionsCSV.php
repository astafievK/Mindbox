<?php

include "config/settings.php";
include "config/headersCSV.php";

function importActionsCSV()
{
    global $headers;
    global $endpointId;
    global $pointOfContact;

    $url = "https://api.mindbox.ru/v3/operations/bulk?endpointId=$endpointId&operation=CustomerActionsImport";

    $csvData = "pointOfContact;ActionDateTimeUtc;ActionTemplateName;CustomerMindboxId\n".
        "$pointOfContact;01.01.2023 12:00;PrivateMeeting;155\n";

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_CAINFO, 'C:/Certificates/cacert.pem');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $csvData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Ошибка запроса: " . curl_error($ch));
    }

    curl_close($ch);

    return json_decode($response, true);
}