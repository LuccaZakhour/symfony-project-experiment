<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function measurementUnits($makeRequest, $db) {
    foreach ($makeRequest('measurementUnits') as $row) {
        $exportData['measurementUnits'][$row['quantityID']] = $row;
    }
    insertIntoTable($db, 'measurementUnits', $exportData['measurementUnits'], 'quantityID');
}

measurementUnits($makeRequest, $db);
