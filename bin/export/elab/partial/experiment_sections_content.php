<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';


$experiment_sections = $db->query('SELECT * FROM experiment_sections')->fetchAll(PDO::FETCH_ASSOC);
    
foreach ($experiment_sections as $experiment_section) {
    $experiment_section_id = $experiment_section['expJournalID'];
    $experiment_id = $experiment_section['experimentID'];
    # /experiments/sections/{expJournalID}/content
    try {
        $experiment_section_content = $makeRequest('experiments/sections/' . $experiment_section_id . '/content');
    } catch (Exception $e) {
        echo 'error: ' . $e->getMessage();
        continue;
    }
    $exportData['experiment_sections_content'][$experiment_section_id] = $experiment_section_content;
    $exportData['experiment_sections_content'][$experiment_section_id]['experimentSectionId'] = $experiment_section_id;
    $exportData['experiment_sections_content'][$experiment_section_id]['experimentId'] = $experiment_id;

    # insert every 100 rows if experimentSectionId and experimentId are not present with same values already
    insertIntoTable($db, 'experiment_sections_content', $exportData['experiment_sections_content'], 'experimentSectionId');
    $exportData['experiment_sections_content'] = [];
    
}

