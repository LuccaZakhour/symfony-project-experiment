<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function notes($makeRequest, $db) {
    foreach ($makeRequest('notes') as $row) {
        $exportData['notes'][$row['noteID']] = $row;
    }
    insertIntoTable($db, 'notes', $exportData['notes'], 'noteID');
}

notes($makeRequest, $db);
