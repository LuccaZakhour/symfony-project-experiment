<?php

require_once __DIR__ . '/../config.php';


$maxRetries = 9; // Maximum number of retries
$retryDelay = 5; // Delay in seconds between retries
$attempt = 0; // Current attempt count

while ($attempt < $maxRetries) {
    try {
        $db = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Attempt to create the database if it doesn't exist
        $db->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Close the previous connection
        $db = null;

        // Reconnect, specifying the newly created or existing database
        $db = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $username, $password, [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Connection successful, exit the loop
        break;
    } catch (PDOException $e) {
        if ($e->getCode() == '08S01' || $e->getMessage() == '1053' || $attempt < $maxRetries - 1) {
            // Specific server shutdown error or other retryable error condition
            $attempt++;
            echo "Connection failed: " . $e->getMessage() . ". Retrying in $retryDelay seconds...\n";
            sleep($retryDelay); // Wait before retrying
        } else {
            // Connection failed after max retries or non-retryable error
            echo "Connection failed: " . $e->getMessage();
            throw $e;
        }
    }
}

if ($db === null) {
    die("Failed to connect to the database after $maxRetries attempts.");
}

function connectToDb($host = null, $username = null, $password = null) {

    if (!isset($host, $username, $password)) {
        // Use global variables if not provided
        global $host, $username, $password;
    }

    return new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}

function createTableIfNeeded($db, $tableName, $columns) {
    try {
        $createQuery = "CREATE TABLE IF NOT EXISTS `$tableName` ($columns)";
        $db->exec($createQuery);
    } catch (PDOException $e) {
        die("Could not create table $tableName: " . $e->getMessage());
    }
}

function insertIntoTable($db, $tableName, array $data, $id = null) {

    foreach ($data as $item) {
        if (!is_array($item)) {
            throw new InvalidArgumentException('Expected $data to be a two-dimensional array.');
        }
    }

    $insertCount = 0;


    foreach ($data as $value) {

        if (isset($id)) {
            // Check if record already exists
            $checkQuery = $db->prepare("SELECT COUNT(*) FROM `$tableName` WHERE {$id} = ?");
            $checkQuery->execute([$value[$id]]);
            if ($checkQuery->fetchColumn() > 0) {
                continue;
            }
        }

        // Enclose column names in backticks
        $columns = implode(', ', array_map(function($col) {
                if ($col === 'order')
                    return "`orderColumn`";
                else
                    return "`$col`"; 
            }, array_keys($value)));


        $placeholders = implode(', ', array_map(function($key) {
            return ':' . $key; // Prefix each key (placeholder) with a colon
        }, array_keys($value)));

        // Construct the INSERT query
        $insertQuery = "INSERT INTO `$tableName` ($columns) VALUES ($placeholders)";
        $stmt = $db->prepare($insertQuery);

        $datetimeColumns = [
            'created', 'statusChanged', 'due', 'lastUpdate', 
            'dateEntered', 'dateOrdered', 'dateReceived', 
            'dateCompleted', 'lastEditDate', 'sectionDate', 
            'logDate', 'stored', 'deleteDate', 'signedOn',
            'updated'
        ];

        foreach ($value as $key => $val) {
            // If the value is an empty string, convert it to NULL
            $val = ($val == '') ? null : $val;

            if (is_array($val)) {
                $val = json_encode($val);
            }

            // If the key matches a datetime column and value is  NULL, format the datetime
            if (in_array($key, $datetimeColumns) && $val !== null) {
                try {
                    $date = new DateTime($val);
                    $val = $date->format('Y-m-d H:i:s'); // Convert to MySQL datetime format
                } catch (Exception $e) {
                    dd($e);
                    // Handle exception if the date format is incorrect
                    // Log the error or set $val to null, depending on your application's requirements
                    // Example: error_log("Date conversion error for $key with value $val: " . $e->getMessage());
                    $val = null; // Setting to null or handle as required
                }
            } else if ($val === '') {
                $val = null;
            }

            $stmt->bindValue(":$key", $val);
        }
        
        try {
            if ($stmt->execute()) {
                $insertCount++;
            }
        } catch (PDOException $e) {
            dump('$value', $value);
            dump('$columns', $columns);
            dump('$placeholders', $placeholders);
            dump('$insertQuery', $insertQuery);
            dd($e);
            echo "Insertion failed: " . $e->getMessage();
        }
    }

    return $insertCount;
}

function table_in_db_exists($db, $tableName, $compareCount = 1) {
    $query = $db->prepare("SHOW TABLES LIKE ?");
    $query->execute([$tableName]);
    if ($query->rowCount() > 0) {
        // Table exists, now check for data
        $dataResult = $db->query("SELECT COUNT(*) FROM `$tableName`");
        $count = $dataResult->fetchColumn();
        return $count >= $compareCount;
    }
    return false;
}

$tables = [
    'groups' => 'groupID INT,
                userID INT, 
                publicJoin TINYINT(1) NULL, 
                logo TEXT, 
                name VARCHAR(255), 
                description TEXT, 
                url TEXT,
                created DATETIME',
    'users' => 'userID INT,
                email VARCHAR(255), 
                firstName VARCHAR(255), 
                lastName VARCHAR(255), 
                role VARCHAR(100), 
                pictureURL TEXT, 
                groupId INT, 
                fullName VARCHAR(255)',
    'projects' => 'projectID INT, 
                groupName VARCHAR(255), 
                numStudies INT, 
                groupID INT, 
                userID INT, 
                creatorID INT, 
                name VARCHAR(255), 
                longName TEXT, 
                description TEXT, 
                notes TEXT, 
                created DATETIME, 
                active TINYINT(1) NULL',
    'studies' => 'studyID INT, 
                projectID INT, 
                groupID INT, 
                subgroupID INT, 
                userID INT, 
                name VARCHAR(255), 
                studyStatus TEXT, 
                experimentCount INT, 
                meta LONGTEXT, 
                statusChanged DATETIME, 
                description TEXT, 
                notes TEXT, 
                approve VARCHAR(100), 
                created DATETIME, 
                deleted TINYINT(1) NULL',        
    'experiments' => 'experimentID INT, 
                usesSignatureWorkflow TINYINT(1) NULL, 
                studyID INT, 
                studyName VARCHAR(255), 
                projectID INT, 
                projectName VARCHAR(255), 
                experimentStatusID INT, 
                experimentGroupID INT, 
                experimentColor VARCHAR(7), 
                experimentStatus TEXT, 
                experimentStatusType VARCHAR(100), 
                signatureStatus VARCHAR(100), 
                groupID INT, 
                subgroupID INT, 
                userID INT, 
                workflowStepID INT, 
                name VARCHAR(255), 
                description TEXT, 
                notes TEXT, 
                created DATETIME, 
                statusChanged DATETIME, 
                dependencyExperimentID INT, 
                due DATETIME, 
                deleted TINYINT(1) NULL, 
                template TINYINT(1) NULL',

    'notes' => 'noteID INT, 
            userID INT, 
            created DATETIME, 
            lastUpdate DATETIME, 
            title VARCHAR(255),
            textBlocks TEXT',

    'measurementUnits' => 'quantityID INT, 
            quantityName VARCHAR(255), 
            unitName VARCHAR(255), 
            unitShort VARCHAR(10),
            calculationFactor DOUBLE',

    'protocols' => 'protID INT,
              storageID INT,
              created DATETIME,
              protVersionID INT,
              latestVersionId INT,
              version INT,
              latestVersion INT,
              draft TINYINT(1) NULL,
              isPublic TINYINT(1) NULL,
              deleted TINYINT(1) NULL,
              name VARCHAR(255),
              description TEXT,
              userID INT,
              authorID INT,
              author VARCHAR(255),
              groupID INT,
              subgroupID INT,
              categoryID INT,
              category VARCHAR(255),
              viewCount INT,
              rating INT,
              numSteps INT,
              groupShareCount INT,
              appViewURL TEXT',

    'protocols_categories' => 'protCategoryID INT,
            archived TINYINT(1) NULL,
            protCount INT,
            userID INT,
            name VARCHAR(255),
            groupID INT',

    'quantityType' => 'quantityID INT,
                 quantityName VARCHAR(255),
                 unitName VARCHAR(255),
                 unitShort VARCHAR(10),
                 calculationFactor DOUBLE',
  
  'orders' => 'shoppingItemID INT,
               subgroupID INT,
               userID INT,
               dateEntered DATETIME,
               dateOrdered DATETIME,
               dateReceived DATETIME,
               dateCompleted DATETIME,
               sampleID INT,
               sampleTypeID INT,
               shopItemType VARCHAR(100),
               foreignID INT,
               catalogItemID INT,
               notifyUser TINYINT(1) NULL,
               amount INT,
               status VARCHAR(50)',
  
  'catalogItems' => 'catalogItemID INT,
              groupID INT,
              supplierID INT,
              supplier TEXT,
              catalogNumber VARCHAR(255),
              price DECIMAL(10,2),
              amount INT,
              quantityType VARCHAR(50),
              name VARCHAR(255),
              catalogName VARCHAR(255),
              currency VARCHAR(10),
              details TEXT,
              barcode VARCHAR(255),
              unitShort VARCHAR(50),
              displayUnit VARCHAR(50),
              currencySymbol VARCHAR(10),
              content VARCHAR(255),
              productImage TEXT',
  
  'storage' => 'storageLayerID INT,
              storageTypeID INT,
              groupID INT,
              userID INT,
              name VARCHAR(255),
              deviceType VARCHAR(50),
              deviceTypeID INT,
              storageType TEXT,
              deviceTypeName VARCHAR(255),
              barcode VARCHAR(255),
              status VARCHAR(50),
              storageID INT,
              instituteID INT,
              department VARCHAR(255),
              address VARCHAR(255),
              building VARCHAR(255),
              floor VARCHAR(50),
              room VARCHAR(50),
              notes TEXT,
              updated DATETIME,
              hasPlanner TINYINT(1) NULL,
              hasValidation TINYINT(1) NULL,
              hideFromBrowser TINYINT(1) NULL',
  
  'storageTypes' => 'storageTypeID INT,
              groupID INT,
              userID INT,
              name VARCHAR(255),
              deviceType VARCHAR(50)',
  
  'files' => 'fileID INT,
              filename VARCHAR(255),
              userID INT,
              size INT,
              groupID INT,
              folderID INT,
              created DATETIME,
              location VARCHAR(100),
              extension VARCHAR(10),
              fileType VARCHAR(100),
              icon VARCHAR(255),
              fullFilePath VARCHAR(255) NULL,
              filePath VARCHAR(255) NULL',
  
  'storageLayers' => 'storageLayerID INT,
              userID INT,
              created DATETIME,
              storageID INT,
              barcode VARCHAR(255),
              isGrid TINYINT(1) NULL,
              parentStorageLayerID INT,
              position INT,
              transposed TINYINT(1) NULL,
              dimension TEXT,
              storageLayerDefinitionID INT,
              name VARCHAR(255),
              icon VARCHAR(50),
              maxSize INT',
    'sampleTypes' => 'sampleTypeID INT,
              userID INT,
              groupID INT,
              deleted TINYINT(1) NULL,
              showSectionsInTabs TINYINT(1) NULL,
              hasExpirationDate TINYINT(1) NULL,
              defaultUnitShort VARCHAR(50),
              name VARCHAR(255),
              description TEXT,
              backgroundColor VARCHAR(10),
              foregroundColor VARCHAR(10),
              quantityRequired TINYINT(1) NULL,
              defaultQuantityType VARCHAR(100),
              thresholdEnabled TINYINT(1) NULL,
              defaultQuantityThreshold INT,
              defaultQuantityAmount INT,
              defaultThresholdAction VARCHAR(100),
              defaultUnit VARCHAR(100)',
  
  'storageLayerDefinitions' => 'storageLayerDefinitionID INT,
          storageID INT,
          deleted TINYINT(1) NULL,
          isGrid TINYINT(1) NULL,
          name VARCHAR(255),
          level INT,
          transposed TINYINT(1) NULL,
          maxSize INT,
          icon VARCHAR(255),
          dimension TEXT',

'sampleSeries' => 'seriesID INT,
              userID INT,
              created DATETIME,
              barcode TEXT,
              sampleIDs TEXT,
              name TEXT,
              threshold INT',

'samples' => 'sampleID INT,
            owner VARCHAR(255),
            archived BOOLEAN,
            meta LONGTEXT,
            created DATETIME,
            userID INT,
            creatorID INT,
            storageLayerID INT,
            position INT,
            seriesID INT,
            barcode VARCHAR(255),
            sampleType TEXT,
            sampleTypeID INT,
            checkedOut BOOLEAN,
            parentSampleID INT,
            name VARCHAR(255),
            description TEXT,
            note TEXT',

'experiment_sections' => 'generated_ID INT,
            expJournalID INT,
            experimentID INT,
            userID INT,
            lastEditUserID INT,
            created DATETIME,
            lastEditDate DATETIME,
            sectionType VARCHAR(50),
            sectionOrder INT,
            sectionHeader VARCHAR(255),
            sectionDate DATETIME,
            firstName VARCHAR(50),
            lastName VARCHAR(50),
            orderColumn INT,
            lastEditUserFirstName VARCHAR(50),
            lastEditUserLastName VARCHAR(50),
            deleted TINYINT(1) NULL',

'experiment_collaborators' => 'generated_ID INT,
            userID INT, 
            email VARCHAR(255), 
            firstName VARCHAR(255), 
            lastName VARCHAR(255), 
            pictureURL TEXT,
            groupId INT, 
            fullName VARCHAR(255),
            experimentID INT',

'experiment_logs' => 'generated_ID INT,
                journalLogID INT, 
                userID INT, 
                userFullName VARCHAR(255), 
                log TEXT, 
                associatedForeignID INT, 
                logDate DATETIME, 
                associatedScope VARCHAR(100),
                experimentID INT',

'experiments_templates' => 'creatorID INT, 
                       creatorName VARCHAR(255), 
                       expTemplateLabelID INT, 
                       experimentID INT, 
                       name VARCHAR(255), 
                       created DATETIME',

'systemsettings' => 'systemSettingID INT, 
                `key` VARCHAR(255), 
                value TEXT NULL',

'samplesAndSeries' => 'samples TEXT,
                  sampleID INT,
                  seriesID INT,
                  series TEXT,
                  totalSeriesSize INT,
                  size INT,
                  name VARCHAR(255),
                  sampleType TEXT,
                  storageLayerID INT,
                  storageLayerName VARCHAR(255),
                  position INT,
                  created DATETIME,
                  userID INT,
                  user VARCHAR(255),
                  checkedOut TINYINT(1) NULL,
                  meta LONGTEXT,
                  description TEXT,
                  note TEXT,
                  type VARCHAR(50)',

'samples_viewcolumns' => 'name VARCHAR(255),
                     isMandatory TINYINT(1) NULL,
                     isDefault TINYINT(1) NULL,
                     isMeta TINYINT(1) NULL,
                     context TEXT',

'experiment_sections_files' => 'experimentFileID INT,
            origin VARCHAR(255),
            parentExperimentFileID INT,
            userID INT,
            expJournalID INT,
            experimentID INT,
            storedName VARCHAR(255),
            realName VARCHAR(255),
            fullFilePath VARCHAR(255) NULL,
            filePath VARCHAR(255) NULL,
            `stored` DATETIME,
            SHA256Hash CHAR(64),
            certificateHash CHAR(64),
            fileSize INT,
            orig_meta LONGTEXT NULL',

'experiment_sections_images' => 'experimentFileID INT,
            position INT  NULL,
            description VARCHAR(255) NULL,
            origin VARCHAR(255) NULL,
            parentExperimentFileID INT,
            userID INT  NULL,
            experimentID INT  NULL,
            storedName VARCHAR(255)  NULL,
            realName VARCHAR(255)  NULL,
            fullFilePath VARCHAR(255),
            filePath VARCHAR(255),
            `stored` DATETIME NULL,
            SHA256Hash CHAR(64) NULL,
            certificateHash CHAR(64) NULL,
            fileSize INT NULL,
            expJournalID INT',

'experiment_sections_content' => 'experimentSectionId INT,
            experimentId INT,
            meta LONGTEXT,
            contents LONGTEXT',

'experiment_sections_html' => 'experimentSectionId INT,
            experimentId INT,
            html LONGTEXT',

'protocol_steps' => 'stepID INT,
            protVersionID INT,
            protID INT,
            name VARCHAR(255),
            contents LONGTEXT,
            duration INT,
            orderColumn INT',

'protocol_variables' => 'varID INT,
            protVersionID INT,
            protID INT,
            name VARCHAR(255),
            description TEXT,
            varType VARCHAR(50),
            quantityID INT,
            unit VARCHAR(50),
            contents LONGTEXT,
            dataSetId INT,
            dataSetItems TEXT',

'experiment_sections_samples' => 'experimentSectionId INT,
            experimentId INT,
            owner VARCHAR(255),
            archived TINYINT(1) NULL,
            meta LONGTEXT,
            sampleID INT,
            created DATETIME,
            userID INT,
            creatorID INT,
            storageLayerID INT,
            position INT,
            seriesID INT,
            barcode CHAR(15),
            sampleType TEXT,
            sampleTypeID INT,
            checkedOut TINYINT(1) NULL,
            parentSampleID INT,
            name TEXT,
            description TEXT,
            note TEXT,
            deleteDate DATETIME NULL',

'experiment_signatureWorkflow' => 'experimentID INT,
            signedOn DATETIME,
            signee TEXT',

'sample_meta' => 'id INT,
             sampleID INT,
             archived TINYINT(1) NULL,
             sampleMetaID BIGINT NULL,
             truncated TINYINT(1) NULL,
             sampleDataType VARCHAR(255) NULL,
             samples TEXT NULL,
             files TEXT NULL,
             sampleTypeMetaDefaultsIDs TEXT NULL,
             sampleTypeMetaID INT NULL,
             `key` VARCHAR(255) NULL,
             value MEDIUMTEXT NULL',

'sampleTypes_meta' => 'sampleTypeMetaID INT,
            sampleTypeID INT,
            MaskRegExp TEXT,
            validationScript TEXT,
            showInlineImages TINYINT(1) NULL,
            sampleTypeMetaDefaultsIDs TEXT,
            `key` TEXT,
            sampleDataType TEXT,
            required TINYINT(1) NULL,
            notes TEXT,
            orderColumn INT,
            defaultValue TEXT,
            defaultYear INT,
            defaultMonth INT,
            defaultDay INT,
            defaultHour INT,
            defaultMinute INT,
            defaultSecond INT,
            fileMask TEXT,
            optionValues TEXT',
];

// Note: This is an example showing the conversion for a subset of tables.
// Repeat the pattern for the rest of your table definitions, adjusting data types and constraints as necessary.


foreach ($tables as $tableName => $columns) {
    createTableIfNeeded($db, $tableName, $columns);
}

