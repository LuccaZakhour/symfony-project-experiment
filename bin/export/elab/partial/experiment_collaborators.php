<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/helper_functions.php';
require_once __DIR__ . '/../lib/mysql_insert.php';

subpage_scrape($db, $tables, $makeRequest, $exportData, 'experiment_collaborators', 'experimentID', 'experiments', 'generated_ID', 'collaborators');

