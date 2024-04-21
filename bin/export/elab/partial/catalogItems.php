<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function catalogItems($makeRequest, $db) {
    foreach ($makeRequest('supplies/catalogItems') as $row) {
        foreach ($makeRequest('supplies/catalogItems') as $row) {
            $exportData['catalogItems'][$row['supplierID']] = $row;
        }
        insertIntoTable($db, 'catalogItems', $exportData['catalogItems'], 'supplierID');
    }
}

catalogItems($makeRequest, $db);