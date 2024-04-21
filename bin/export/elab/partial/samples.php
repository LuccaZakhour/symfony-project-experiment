<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function samples($makeRequest, $db) {
    foreach ($makeRequest('samples') as $row) {
        $exportData['samples'][$row['sampleID']] = $row;
    }
    insertIntoTable($db, 'samples', $exportData['samples'], 'sampleID');

    $samples = $db->query('SELECT * FROM samples')->fetchAll(PDO::FETCH_ASSOC);

    foreach ($samples as $sample) {
        // samples/15194563/meta
        $exportData['sample_meta'] = [];
        $data = $makeRequest('samples/'. $sample['sampleID'] .'/meta');
        foreach($data as $key => $value) {
            $value['sampleID'] = $sample['sampleID'];
            $exportData['sample_meta'][$key] = $value;
        }

        insertIntoTable($db, 'sample_meta', $exportData['sample_meta'], 'sampleMetaID');
    }
}

samples($makeRequest, $db);
