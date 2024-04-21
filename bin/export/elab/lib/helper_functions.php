<?php

use GuzzleHttp\Client;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

function subpage_scrape($db, $tables, $makeRequest, $exportData, $subpageKey = 'experimentSections', 
    $parentKey = 'experimentID', $parentTable = 'experiments', $subpageId = 'sectionID',
    $subpageTable = 'sections') {

    createTableIfNeeded($db, $subpageKey, $tables[$subpageKey]);
    // get experiments from db, 100 at a time, make it so it can be continued if it fails, use while loop
    while (true) {
        $lastExperimentSection = $db->query('SELECT * FROM `' . $subpageKey . '` ORDER BY ' . $subpageId . ' DESC LIMIT 1')->fetch();
        if (!is_array($lastExperimentSection)) {
            // Handle the case where no experiment sections were found
            $lastExperimentSection = [$subpageId => 0, $parentKey => 0];
        }
        $lastExperimentSectionId = isset($lastExperimentSection[$subpageId]) ? $lastExperimentSection[$subpageId] : 0;

        $experiments = $db->query('SELECT * FROM `' . $parentTable . '` WHERE ' . $parentKey . ' > ' . $lastExperimentSection[$parentKey] ?? 0 . ' LIMIT 50')->fetchAll();

        if (count($experiments) == 0) {
            break;
        }

        $i = $lastExperimentSectionId ?? 0;

        foreach ($experiments as $experiment) {

            foreach ($makeRequest($parentTable . '/' . $experiment[$parentKey] . '/' . $subpageTable) as $experimentSection) {
                $i++;

                # if $parentKey not in $experimentSection, add it from $experiment[$parentKey] and save it as $experimentSection[$parentKey]
                if (!isset($experimentSection[$parentKey])) {
                    $experimentSection[$parentKey] = $experiment[$parentKey];
                }

                $experimentSection[$subpageId] = $i;

                $exportData[$subpageKey][$i] = $experimentSection;
            }
            
            insertIntoTable($db, $subpageKey, $exportData[$subpageKey], $subpageId);
            $exportData[$subpageKey] = [];
        }
    }
}


function logMessage($message, $filePath = '', $line = '') {
    $timestamp = date('Y-m-d H:i:s');
    $context = sprintf("[%s] %s - %s:%d", $timestamp, $message, $filePath, $line);
    echo $context;
    
    $datetime = date('j_n_Y_H_i_s');
    // Writing to a file named script.log in the current directory
    file_put_contents(__DIR__ . "/export_{$datetime}.log", $context . "\n", FILE_APPEND);
}



$client = new Client([
    'base_uri' => $url,
    'timeout'  => 120.0, // Timeout set to 120 seconds
    'connect_timeout' => 120.0,
    'headers' => [
        'Authorization' => $apiKey,
    ]
]);

$makeRequest = function ($uri, array $params = []) use ($client) {

    $result = [];

    $page = 0;
    do {
        echo 'request: ' . $uri . ' / page = ' . $page . PHP_EOL;
        
        if ($page == 0) {
            $responseRaw = $client->get($uri);
        } else {
            $responseRaw = $client->get($uri, [
                'query' => [
                    '$page' => $page
                ]
            ]);
        }

        if ($responseRaw->getStatusCode() != '200') {
            die('error: status = ' . $responseRaw->getStatusCode());
        }

        // if its a file, return filename and contents
        if (strpos($uri, 'files/') !== false || strpos($uri, '/excel') !== false || strpos($uri, '/images/') !== false) {
            $headers = $responseRaw->getHeaders();

            if (array_key_exists('Content-Disposition', $headers)) {
                $contentDisposition = $headers['Content-Disposition'][0];
                $filename = substr($contentDisposition, strpos($contentDisposition, 'filename=') + 10, -1);

                echo 'filename = ' . $filename . PHP_EOL;
                $fileContent = $responseRaw->getBody()->getContents();

                return [
                    'uri' => $uri,
                    'filename' => $filename,
                    'contents' => $fileContent
                ];
            }
        }

        // if /html, return html
        if (strpos($uri, '/html') !== false || strpos($uri, '/handlers/getProtocol') !== false) {
            $fileContent = $responseRaw->getBody()->getContents();

            return $fileContent;
        }

        $response = json_decode($responseRaw->getBody()->getContents(), true);
        if (array_key_exists('data', $response)) {
            $result = array_merge($result, $response['data']);

            $currentPage = $response['currentPage'];
            $recordCount = $response['recordCount'];
            $maxRecords = $response['maxRecords'];
            $totalRecords = $response['totalRecords'];
            echo '$currentPage = ' . $currentPage . PHP_EOL;
            echo '$recordCount = ' . $recordCount . PHP_EOL;
            echo '$maxRecords = ' . $maxRecords . PHP_EOL;
            echo '$totalRecords = ' . $totalRecords . PHP_EOL;

            ++$page;
        } else {
            $result = $response;
            break;
        }
    } while ($recordCount == $maxRecords);

    return $result;
};


// Function to wait for at least one process to complete
function waitForProcessCompletion(&$activeProcesses, &$completedTasks) {
    $index = null;
    while ($index === null) {
        foreach ($activeProcesses as $i => $process) {
            if (!$process->isRunning()) {
                // Process has finished, handle output and errors as before
                $completedTasks[] = getTaskNameFromProcess($process); // Implement this based on your command structure
                $index = $i;
                break;
            }
        }
        usleep(100000); // Sleep to reduce CPU load
    }
    unset($activeProcesses[$index]); // Remove finished process from active processes
}

// Implement this function based on your needs to extract the task name from a Process object
function getTaskNameFromProcess(Process $process) {
    // Example implementation, adjust according to how you define your command
    $commandLine = $process->getCommandLine();
    preg_match('/partial\/(.*?)\.php/', $commandLine, $matches);
    return $matches[1] ?? 'unknown';
}

