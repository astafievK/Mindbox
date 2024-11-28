<?php

require 'functions/getUserById.php';
require 'functions/registration.php';
require 'functions/importClientsFromCSV.php';


$filePath = "C:/Users/astafiev/Desktop/example.csv";
try {
    echo importClientsFromCSV($filePath);
} catch (Exception $e) {

}