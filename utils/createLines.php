<?php

include "config/dictionaries.php";
include "utils/formatDateTime.php";

function createLines($queryData) {
    global $orderLineStatuses;

    $lines = [];
    $lineNumber = 1;

    foreach ($queryData as $row) {
        $lines[] = [
            "basePricePerItem" => "0",
            "quantity" => "1",
            "lineNumber" => (string)$lineNumber,
            "product" => [
                "ids" => [
                    "c1" => "0"
                ]
            ],
            "status" => $orderLineStatuses[$row['ВидСобытия']],
            "customFields" => [
                "eventUID" => $row['EventUID'],
                "eventManager" => $row['Менеджер'],
                "eventDateTime" => formatDateTime($row['ДатаНачала'], $row['ВремяНачала'])
            ]
        ];

        $lineNumber++;
    }

    return $lines;
}
