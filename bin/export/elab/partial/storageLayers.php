<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function storageLayers($makeRequest, $db) {

    foreach ($makeRequest('storageLayers') as $row) {
        $exportData['storageLayers'][$row['storageLayerID']] = $row;
    }
    foreach ($makeRequest('storageLayers') as $row) {
        insertIntoTable($db, 'storageLayers', $exportData['storageLayers'], 'storageLayerID');
        
        $storageLayers = $db->query('SELECT * FROM storageLayers')->fetchAll(PDO::FETCH_ASSOC);

        foreach($storageLayers as $storageLayer) {
            $storageLayerId = $storageLayer['storageLayerID'];
            # if $makeRequest returns an exception, continue with other id
            try {
                $exportData['storageLayerDefinitions'][$storageLayerId] = $makeRequest('/api/v1/storage/' . $storageLayerId . '/storageLayerDefinitions');
            } catch (Exception $e) {
                echo 'error: ' . $e->getMessage();
                continue;
            }

            insertIntoTable($db, 'storageLayerDefinitions', $exportData['storageLayerDefinitions'], 'storageLayerDefinitionID');
            $exportData['storageLayerDefinitions'] = [];
        }
    }
}

storageLayers($makeRequest, $db);
