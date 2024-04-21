<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function projects($makeRequest, $db) {
    foreach ($makeRequest('projects') as $project) {
        $exportData['projects'][$project['projectID']] = $project;
    }
    insertIntoTable($db, 'projects', $exportData['projects'], 'projectID');
}

projects($makeRequest, $db);
