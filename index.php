<?php

require 'functions/getOrderById.php';
require 'functions/editOrder.php';
require 'functions/createOrder.php';
require 'functions/editClient.php';

try {
    editClient("122");
    // getOrderById("122", "107");
    //echo createOrder("122");
    //echo editOrder("122", "107", "1488");
} catch (Exception $e) {
    echo $e->getMessage();
}