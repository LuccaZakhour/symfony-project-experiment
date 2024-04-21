<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';


$experiment_sections = $db->query('SELECT * FROM experiment_sections')->fetchAll(PDO::FETCH_ASSOC);

# do the same for $experiment_sections_samples = $makeRequest('experiments/sections/' . $experiment_section_id . '/samples');
foreach ($experiment_sections as $experiment_section) {
    $experiment_section_id = $experiment_section['expJournalID'];
    $experiment_id = $experiment_section['experimentID'];
    # /experiments/sections/{expJournalID}/samples
    try {
        $experiment_section_contents = $makeRequest('experiments/sections/' . $experiment_section_id . '/samples');
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
        continue;
    }

    foreach($experiment_section_contents as $experiment_section_content) {
        $exportData['experiment_sections_samples'][$experiment_section_id] = $experiment_section_content;
        $exportData['experiment_sections_samples'][$experiment_section_id]['experimentSectionId'] = $experiment_section_id;
        $exportData['experiment_sections_samples'][$experiment_section_id]['experimentId'] = $experiment_id;
        
        try {
            echo '.';
            insertIntoTable($db, 'experiment_sections_samples', $exportData['experiment_sections_samples'], 'barcode');
            $exportData['experiment_sections_samples'] = [];
        } catch (Exception $e) {
            echo 'error: ' . $e->getMessage();

            continue;
        }
    }
}
