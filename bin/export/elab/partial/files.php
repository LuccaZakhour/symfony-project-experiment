<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';


foreach ($makeRequest('files') as $file) {
    
    try {

        $fileId = $file['fileID'];

        // make request to files/275460
        $response = $makeRequest('files/' . $file['fileID']);

        $filePath = 'clientId/' . $clientId . '/fileId/' . $fileId;
        $directoryPath = __DIR__ . '/../files/' . $filePath;

        $directoryPath = realpath($directoryPath);

        // Create the directory if it doesn't exist
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        $fullFilePath = $directoryPath . '/' .  $response['filename'];

        file_put_contents($fullFilePath, $response['contents']);

        echo '.';

        // add to $file fullFilePath and filePath
        $file['fullFilePath'] = $fullFilePath;
        $file['filePath'] = $filePath;

        insertIntoTable($db, 'files', [$file], 'fileID');
    } catch (Exception $e) {
        dump('$e', $e);
        continue;
    }

}




