<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function samples_viewcolumns($makeRequest, $db) {
    $i = 0;
    foreach ($makeRequest('samples/viewcolumns') as $data) {
        $i++;
        $exportData['samples_viewcolumns'][$i] = $data;
    }
    table_in_db_exists($db, 'samples_viewcolumns') || insertIntoTable($db, 'samples_viewcolumns', $exportData['samples_viewcolumns']);
}

samples_viewcolumns($makeRequest, $db);