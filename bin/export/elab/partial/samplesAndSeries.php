<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function samplesAndSeries($makeRequest, $db) {
    $i = 0;
    foreach ($makeRequest('samplesAndSeries') as $data) {
        $i++;
        $exportData['samplesAndSeries'][$i] = $data;
    }
    table_in_db_exists($db, 'samplesAndSeries') || insertIntoTable($db, 'samplesAndSeries', $exportData['samplesAndSeries']);
}

samplesAndSeries($makeRequest, $db);