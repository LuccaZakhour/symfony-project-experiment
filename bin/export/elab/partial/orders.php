<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';


function orders($makeRequest, $db) {
    foreach ($makeRequest('supplies/orders') as $row) {
        foreach ($makeRequest('supplies/orders') as $row) {
            $exportData['orders'][$row['shoppingItemID']] = $row;
        }
        insertIntoTable($db, 'orders', $exportData['orders'], 'shoppingItemID');
    }
}

orders($makeRequest, $db);
