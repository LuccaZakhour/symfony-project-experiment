<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function experiments_templates($makeRequest, $db) {
    foreach ($makeRequest('experiments/templates') as $row) {
        $i = 0;

        $exportData['experiments_templates'][++$i] = $row;
    } 
    insertIntoTable($db, 'experiments_templates', $exportData['experiments_templates'], 'name');
}

experiments_templates($makeRequest, $db);