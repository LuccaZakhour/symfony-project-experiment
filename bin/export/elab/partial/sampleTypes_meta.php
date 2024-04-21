<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function sampleTypes_meta($makeRequest, $db) {
    foreach ($makeRequest('sampleTypes/meta') as $row) {
        $exportData['sampleTypes_meta'][$row['sampleTypeID']] = $row;
    }
    insertIntoTable($db, 'sampleTypes_meta', $exportData['sampleTypes_meta'], 'sampleTypeMetaID');
}

sampleTypes_meta($makeRequest, $db);
