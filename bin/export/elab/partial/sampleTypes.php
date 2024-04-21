<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function sampleTypes($makeRequest, $db) {
    foreach ($makeRequest('sampleTypes') as $row) {
        $exportData['sampleTypes'][$row['sampleTypeID']] = $row;
    }
    insertIntoTable($db, 'sampleTypes', $exportData['sampleTypes'], 'sampleTypeID');
}

sampleTypes($makeRequest, $db);