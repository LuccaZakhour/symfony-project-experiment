<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function quantityType($makeRequest, $db) {
    foreach ($makeRequest('quantityType') as $row) {
        $exportData['quantityType'][$row['quantityID']] = $row;
    }
    insertIntoTable($db, 'quantityType', $exportData['quantityType'], 'quantityID');    
}

quantityType($makeRequest, $db);
