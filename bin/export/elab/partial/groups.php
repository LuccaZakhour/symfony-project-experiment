<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function groups($makeRequest, $db) {
    foreach ($makeRequest('groups') as $group) {
        $exportData['groups'][$group['groupID']] = $group;

        foreach ($makeRequest('groups/members?groupID=' . $group['groupID']) as $user) {
            $exportData['users'][$user['userID']] = $user;
        }
        insertIntoTable($db, 'users', $exportData['users'], 'userID');
    }
    insertIntoTable($db, 'groups', $exportData['groups'], 'groupID');
}

groups($makeRequest, $db);
