<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function storageTypes($makeRequest, $db) {
    foreach ($makeRequest('storageTypes') as $row) {
        $exportData['storageTypes'][$row['storageTypeID']] = $row;
    }
    insertIntoTable($db, 'storageTypes', $exportData['storageTypes'], 'storageTypeID');
}

storageTypes($makeRequest, $db);
