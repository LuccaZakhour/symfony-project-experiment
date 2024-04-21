<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function experiment_signatureWorkflow($makeRequest, $db) {

    $startTime = microtime(true);

    $experiments = $db->query('SELECT * FROM experiments')->fetchAll(PDO::FETCH_ASSOC);

    foreach ($experiments as $experiment) {
        $data = $makeRequest('experiments/'. $experiment['experimentID'] .'/signatureWorkflow');
        foreach($data as $key => $value) {
            $value['experimentID'] = $experiment['experimentID'];
            $exportData['experiment_signatureWorkflow'][$key] = $value;
        }

        insertIntoTable($db, 'experiment_signatureWorkflow', $exportData['experiment_signatureWorkflow'], 'experimentID');
    }

    echo 'experiment_signatureWorkflow: ' . (microtime(true) - $startTime) . ' seconds' . PHP_EOL;
}

experiment_signatureWorkflow($makeRequest, $db);