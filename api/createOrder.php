<?php

require 'vendor/autoload.php';

use Ramsey\Uuid\Uuid;

include "config/settings.php";
include "config/headersJSON.php";
include "queries/getActionsByUid.php";
include "utils/createLines.php";
include "utils/checkOrderAlreadyExists.php";
include "api/editOrder.php";

function createOrder($clientData, $allClientOrders){
    global $headers;
    global $endpointId;

    try {
        $transactionId = Uuid::uuid4()->toString();
    } catch (Exception $e) {
        die("Ошибка создания UUID: ". $e->getMessage());
    }

    $url = "https://api.mindbox.ru/v3/operations/sync?endpointId=$endpointId&operation=CreateOrder&transactionId=$transactionId";

    $date = $clientData['ДатаСобытия'];
    $time = $clientData['ВремяСобытия'];
    $completeDateTimeUtc = formatDateTime($date, $time);

    $mindboxId = checkOrderAlreadyExists($clientData['РабочийЛист'], $allClientOrders);
    
    if(isset($mindboxId)){
        echo editOrder($clientData, $mindboxId);
        return;
    }

    $data = [
        "customer" => [
            "mobilePhone" => $clientData['Телефон'],
        ],
        "executionDateTimeUtc" => "$completeDateTimeUtc",
        "order" => [
            "ids" => [
                "externalOrderId" => $clientData['РабочийЛист'],
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
        return "Ошибка добавления заказа";
    } else {
        echo "Заказ добавлен/обновлен\n";
    }
}