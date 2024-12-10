<?php

include "config/settings.php";
include "config/headersJSON.php";

function editOrder($clientData, $orderMindboxId){
    global $headers;
    global $endpointId;

    $url = "https://api.mindbox.ru/v3/operations/sync?endpointId=$endpointId&operation=editOrder";

    $date = $clientData['ДатаСобытия'];
    $time = $clientData['ВремяСобытия'];
    $completeDateTimeUtc = formatDateTime($date, $time);

    $data = [
        "customer" => [
            "mobilePhone" => $clientData['Телефон'],
        ],
        "executionDateTimeUtc" => "$completeDateTimeUtc",
        "order" => [
            "ids" => [
                "externalOrderId" => $clientData['РабочийЛист'],
                "mindboxId" => $orderMindboxId
            ],
            "lines" => createLines(getActionsByUid($clientData['РабочийЛист'])),
            "customFields" => [
                "orderStatus" => $clientData['РабочийЛистСтатус'],
                "workSheetUID" => $clientData['РабочийЛист'],
                "orderEventType" => $clientData['ВидСобытия'],
                "orderOrganization" => $clientData['Организация']
            ]
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

    $response = json_decode(curl_exec($ch), true);

    if (curl_errno($ch)) {
        throw new Exception("Ошибка запроса: " . curl_error($ch));
    }

    curl_close($ch);

    if ($response['status'] != 'Success') {
        return "Ошибка обновления заказа\n";
    } else {
        return "Заказ обновлен\n";
    }
}