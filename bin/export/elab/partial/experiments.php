<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function experiments($makeRequest, $db, $tables, $clientId) {

    $startTime = microtime(true);

    // get database name from $db
    foreach ($makeRequest('experiments') as $row) {
        $exportData['experiments'][$row['experimentID']] = $row;
    }
    
    insertIntoTable($db, 'experiments', $exportData['experiments'], 'experimentID');
    
    $endTime = microtime(true);
    // Calculate elapsed time
    $elapsedTime = $endTime - $startTime; // Time in seconds
    $hours = floor($elapsedTime / 3600);
    $minutes = floor(($elapsedTime / 60) % 60);
    $seconds = $elapsedTime % 60;

    // Output the elapsed time
    echo sprintf("Elapsed Time experiments.php: %02d:%02d:%02d", $hours, $minutes, $seconds);
    
}

experiments($makeRequest, $db, $tables, $clientId);

