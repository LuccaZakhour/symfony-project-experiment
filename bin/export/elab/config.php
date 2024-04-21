<?php

# show all errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Symfony\Component\Dotenv\Dotenv;

require __DIR__.'/../../../vendor/autoload.php';

$exportData = [
    'groups' => [],
    'users' => [],
    'projects' => [],
    'studies' => [],
    'experiments' => [],
    'notes' => [],
    'measurementUnits' => [],
    'protocols' => [],
    'protocols_categories' => [],
    'quantityType' => [],
    'sampleTypes' => [],
    'samples' => [],
    'orders' => [],
    'catalogItems' => [],
    'storageTypes' => [],
    'storage' => [],
    'storageLayers' => [],
    'storageLayerDefinitions' => [],
    'sampleSeries' => [],
    'experiment_sections' => [],
    'experiment_collaborators' => [],
    'experiment_logs' => [],
    'experiment_signatureWorkflow' => [],
    'experiments_templates' => [],
    'systemsettings' => [],
    'samplesAndSeries' => [],
    'samples_viewcolumns' => [],
    'experiment_sections_content' => [],
    'experiment_sections_html' => [],
    'experiment_sections_samples' => [],
    'files' => [],
    'protocol_steps' => [],
    'sample_meta' => [],
    'sampleTypes_meta' => [],
];

$url = 'https://www.elabjournal.com/api/v1/';
$apiKey = '6cf4057286ff220af8fbf8dc36d3e1ef'; # TODO: make this an option;
$clientId = 1; # TODO: make this an option;
$phpBinary = 'php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/../../../.env'); // Adjust the path to your .env file as necessary

// Now, you can access your environment variables
$host = $_ENV['EXPORT_DB_HOST'] ?? 'localhost';
$dbNamePrefix = $_ENV['EXPORT_DB_NAME_PREFIX'] ?? 'dev_export_labowl_db_testing_';
if (isset($databaseName) && $databaseName) {
    $dbName = $dbNamePrefix . $databaseName;
} else {
    $date = date('j_n_Y');
    #$date = '28_2_2024'; # TODO: remove, for testing only
    $dbName = $dbNamePrefix . $date;
}

if (isset($_ENV['EXPORT_DB_NAME'])) {
    $dbName = $_ENV['EXPORT_DB_NAME'];
}

$username = $_ENV['EXPORT_DB_USER'] ?? 'root';
$password = $_ENV['EXPORT_DB_PASSWORD'] ?? '';

echo "Creating database $dbName\n";
