<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

# TODO: make experiment_sections scrape on multiple threads
subpage_scrape($db, $tables, $makeRequest, $exportData, 'experiment_sections', 'experimentID', 'experiments', 'generated_ID', 'sections');
