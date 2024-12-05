<?php

require 'api/getClientById.php';
require 'api/editOrder.php';
require 'api/createOrder.php';
require 'api/editClient.php';
require 'api/clientConfirmSubscribtion.php';
require 'utils/getClientUUID.php';
require 'api/editClientStatusInOrder.php';
require 'api/editOrderStatus.php';


try {
    //echo editClient("122");
    //echo getOrderById("122", "107");
    //echo createOrder("122");
    //echo editOrder("122", "107", "1488");
    //echo getClientUUID(getClientById("122"));
    //echo clientConfirmSubscribtion("122");
    //echo editOrderStatus("117", "7");
    echo editClientStatusInOrder("117", "2");
} catch (Exception $e) {
    echo $e->getMessage();
}