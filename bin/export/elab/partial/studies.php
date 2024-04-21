<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function studies($makeRequest, $db) {
    foreach ($makeRequest('studies') as $study) {
        $exportData['studies'][$study['studyID']] = $study;
    }
    insertIntoTable($db, 'studies', $exportData['studies'], 'studyID');
}

studies($makeRequest, $db);
