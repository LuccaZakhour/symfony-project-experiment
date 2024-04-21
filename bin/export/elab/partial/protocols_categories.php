<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function protocols_categories($makeRequest, $db) {
    foreach ($makeRequest('protocols/categories') as $row) {
        $exportData['protocols_categories'][$row['protCategoryID']] = $row;
    }
    insertIntoTable($db, 'protocols_categories', $exportData['protocols_categories'], 'protCategoryID');    
}

protocols_categories($makeRequest, $db);
