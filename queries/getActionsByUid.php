<?php

function getActionsByUid($workSheetUid) {
    $dsn = "sqlsrv:Server=SRVMARKETOLOG;Database=Mindbox";
    $username = "sa";
    $password = "123aA123";

    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "
        SELECT *
        FROM [Mindbox].[dbo].[AllEventData]
        WHERE [РабочийЛистУИД] = :workSheetUid
        ORDER BY [ДатаНачала] DESC, [ВремяНачала] DESC;
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':workSheetUid', $workSheetUid, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Ошибка подключения: " . $e->getMessage();
    }
}
