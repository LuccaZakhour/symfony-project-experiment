<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function systemsettings($makeRequest, $db) {
    foreach ($makeRequest('systemsettings') as $row) {
        $exportData['systemsettings'][$row['systemSettingID']] = $row;
    }
    insertIntoTable($db, 'systemsettings', $exportData['systemsettings'], 'systemSettingID');
}

systemsettings($makeRequest, $db);
