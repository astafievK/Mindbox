<?php

require "api/registration.php";
require "api/createOrder.php";
require "api/getClientByPhoneNumber.php";

function getClients(){
    $dsn = "sqlsrv:Server=SRVMARKETOLOG;Database=Mindbox";
    $username = "sa";
    $password = "123aA123";

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $sql = "
        WITH RankedPhones AS (
            SELECT 
			[ДатаСобытия],
			[ВремяСобытия],
            [ВидСобытия],
            [РабочийЛист],
			[РабочийЛистСтатус],
            [Телефон],
            [Организация],
            [Клиент],
            [ЭлПочты],
            ROW_NUMBER() OVER (PARTITION BY [Телефон] ORDER BY [ДатаСобытия] DESC) AS rn
            FROM [Mindbox].[dbo].[EventData]
            WHERE [Телефон] IS NOT NULL 
            AND [Телефон] <> ''
            AND NOT (
                [Телефон] LIKE '7%' OR 
                [Телефон] LIKE '+7%' OR 
                [Телефон] LIKE '8%' OR 
                [Телефон] LIKE '+8%'
            )
            AND LEN(REPLACE(REPLACE(REPLACE(REPLACE([Телефон], '+', ''), '(', ''), ')', ''), '-', '')) = 10
        )
        SELECT TOP 10
		[ДатаСобытия],
		[ВремяСобытия],
        [ВидСобытия],
        [РабочийЛист],
		[РабочийЛистСтатус],
        '7' + REPLACE(REPLACE(REPLACE(REPLACE(REPLACE([Телефон], '+', ''), '(', ''), ')', ''), '-', ''), ' ', '') AS [Телефон],
        [Организация],
        [Клиент],
        [ЭлПочты]
        FROM RankedPhones
        WHERE rn = 1
        AND [Клиент] NOT LIKE '%[^а-яА-Я0-9 ]%'
        ORDER BY [Телефон];
        ";
    
        $stmt = $pdo->query($sql);

        if ($stmt === false) {
            throw new Exception("Ошибка работы запроса: " . implode(", ", $pdo->errorInfo()));
        }
    
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $dataSize = count($data);
        $i = 1;

        foreach ($data as $row) {
            echo "--------------------------\n";
            echo "[$i/$dataSize] " . $row['РабочийЛист'] . "\n";

            $checkClientExists = getClientByPhoneNumber($row['Телефон']);
            $allClientOrders = getClientOrders($row['Телефон']);

            if ($checkClientExists != 1) {
                echo registration($row['Телефон'], $row['ЭлПочты'], $row['Клиент']) . "\n";
            }

            createOrder($row, $allClientOrders);
            $i++;
        }
    
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Ошибка выполнения запроса: " . $e->getMessage();
    }
}