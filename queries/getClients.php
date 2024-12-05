<?php

$dsn = "sqlsrv:Server=SRVMARKETOLOG;Database=NewEventDatabase";
$username = "sa";
$password = "123aA123";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "
    WITH RankedPhones AS (
        SELECT 
            [Телефон],
            [Клиент],
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
    SELECT 
        REPLACE(REPLACE(REPLACE(REPLACE(REPLACE([Телефон], '+', ''), '(', ''), ')', ''), '-', ''), ' ', '') AS [Телефон],
        [Клиент]
    FROM RankedPhones
    WHERE rn = 1
    ORDER BY [Телефон];
    ";

    $stmt = $pdo->query($sql);

    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[] = $row;
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}

$conn = null;