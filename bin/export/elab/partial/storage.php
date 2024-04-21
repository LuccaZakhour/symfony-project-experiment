<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function storage($makeRequest, $db) {
    foreach ($makeRequest('storage') as $row) {
        $exportData['storage'][$row['storageID']] = $row;
    }
    insertIntoTable($db, 'storage', $exportData['storage'], 'storageID');
}

storage($makeRequest, $db);
