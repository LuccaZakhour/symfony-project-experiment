<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';


$experiment_sections = $db->query('SELECT * FROM experiment_sections')->fetchAll(PDO::FETCH_ASSOC);

function scrape_experiment_sections__excel($experiment_sections, $makeRequest, $clientId) {
    foreach ($experiment_sections as $experiment_section) {
        $experiment_section_id = $experiment_section['expJournalID'];
        $experiment_id = $experiment_section['experimentID'];
        # /experiments/sections/{expJournalID}/content
        try {
            $experiment_section_excel = $makeRequest('experiments/sections/' . $experiment_section_id . '/excel');

            $experiment_section_excel_content = $experiment_section_excel['contents'];

            /*
            {
                "cache-control": "no-cache",
                "content-disposition": "inline; filename=\"09.05.2019- DNA Transfection.xlsx\"",
                "content-type": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                "date": "Sat, 16 Dec 2023 19:26:04 GMT",
                "expires": "-1",
                "pragma": "no-cache",
                "server": "Microsoft-IIS/10.0",
                "x-aspnet-version": "4.0.30319",
                "x-powered-by": "ASP.NET"
                }
            */
            // get filename from content-disposition
            $filename = $experiment_section_excel['filename'];

        } catch (Exception $e) {
            echo 'error: ' . $e->getMessage();
            continue;
        }
        
        $directoryPath = __DIR__ . '/../files/clientId/' . $clientId . '/experiment_sections/'
             . $experiment_section['expJournalID'] . '/excel/' . $experiment_section_id;

        # Create the directory if it doesn't exist
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0777, true);
        }

        $fullFilePath = $directoryPath . '/' .  $filename;

        $fullFilePath = str_replace(' ', '_', $fullFilePath);

        echo '.';
        file_put_contents($fullFilePath, $experiment_section_excel_content);
    }
}


scrape_experiment_sections__excel($experiment_sections, $makeRequest, $clientId);
