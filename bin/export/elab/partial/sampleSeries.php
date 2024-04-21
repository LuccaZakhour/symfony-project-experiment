<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function sampleSeries($makeRequest, $db) {
    foreach ($makeRequest('sampleSeries') as $row) {
        $exportData['sampleSeries'][$row['seriesID']] = $row;
    }
    insertIntoTable($db, 'sampleSeries', $exportData['sampleSeries'], 'seriesID');
}

sampleSeries($makeRequest, $db);
