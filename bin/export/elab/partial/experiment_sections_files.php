<?php

# show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';


$experiment_sections = $db->query('SELECT * FROM experiment_sections')->fetchAll(PDO::FETCH_ASSOC);


function scrape_experiment_sections_files($db, $makeRequest, $clientId, $experiment_sections) {

    $data['experiment_sections_files'] = [];
    $i = 0;

    # first loop through experiments/sections/4051205/files
    foreach($experiment_sections as $experiment_section) {

        try {
            $experiment_section_files = $makeRequest('experiments/sections/' . $experiment_section['expJournalID'] . '/files');
            #usleep(500000); // Sleep for 0.5 seconds
            #usleep(5000); // Sleep for 0.005 seconds
        } catch (Exception $e) {
            continue;
        }

        # insert into experiment_sections_files
        foreach ($experiment_section_files as $experiment_section_file) {
            $experiment_section_file['experimentFileID'] = $experiment_section_file['experimentFileID'] ?? 0;
            $experiment_section_file['expJournalID'] = $experiment_section['expJournalID'];
            $data['experiment_sections_files'][$experiment_section_file['experimentFileID']] = $experiment_section_file;
        }

        foreach ($experiment_section_files as $experiment_section_file) {

            # if not set $experiment_section['experimentFileID'] continue
            if (!isset($experiment_section_file['experimentFileID'])) {
                continue;
            }

            # experimentFileID
            $experimentFileID = $experiment_section_file['experimentFileID'];
            # expJournalID
            $expJournalID = $experiment_section['expJournalID'];
            # set $filename from realName
            $filename = $experiment_section_file['realName'];

            $directoryPath = __DIR__ . '/../files/clientId/' . $clientId . '/experiment_sections/' .
                $experiment_section['expJournalID'] . '/files/';

                
            # Create the directory if it doesn't exist
            if (!is_dir($directoryPath)) {
                # remove dir $fullFilePath
                
                mkdir($directoryPath, 0777, true);
            }

            // get absolute path for $directoryPath even if files is link
            $directoryPath = realpath($directoryPath) . '/';

            

            $filePath = $experiment_section_file['experimentFileID'] . '_' . $filename;
            $filePath = str_replace(' ', '_', $filePath);


            $fullFilePath = $directoryPath . $filePath;

            if (is_dir($fullFilePath)) {
                rmdir($fullFilePath);
            }

            # make request to experiments/sections/{expJournalID}/files/{experimentFileID}
            try {
                $fileContents = $makeRequest('experiments/sections/' . $expJournalID . '/files/' . $experimentFileID);
            } catch (Exception $e) {
                continue;
            }

            # save file to $fullFilePath
            try {
                file_put_contents($fullFilePath, $fileContents['contents']);
                
                // add wait here to not clutter mysql
                $experiment_section_file['fullFilePath'] = $fullFilePath;
                $experiment_section_file['filePath'] = '/clientId/' . $clientId . '/experiment_sections/' .
                    $experiment_section['expJournalID'] . '/files/' . $filePath;
                $experiment_section_file['expJournalID'] = $expJournalID;
                $experiment_section_file['orig_meta'] = json_encode($experiment_section_file);

                insertIntoTable($db, 'experiment_sections_files', [$experiment_section_file], 'experimentFileID');

                // if $fullFilePath doesn't exist, throw and an error
                if (!file_exists($fullFilePath)) {
                    throw new Exception('File not saved: ' . $fullFilePath);
                }
            } catch (Exception $e) {
                dd('$e', $e);
            }

        }
    }
}


scrape_experiment_sections_files($db, $makeRequest, $clientId, $experiment_sections); # NOTE: this takes a lot of space and time

