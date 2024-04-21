<?php

namespace App\Command\Import;

ini_set('memory_limit', '1024M');

use App\Command\Import\Trait\ImportExportTrait;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use PDO;
use PDOException;
use App\Entity\User;
use App\Entity\Experiment;
use App\Entity\Group;
use App\Entity\Project;
use App\Entity\Study;
use App\Entity\SampleType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\StorageType;
use App\Entity\Protocol;
use App\Entity\ProtocolField;
use App\Entity\Variable;
use App\Entity\Section;
use App\Entity\Sample;
use App\Entity\Storage;
use App\Entity\SampleSeries;
use App\Entity\CatalogItem;
use App\Service\PositionManager;
use App\Command\Import\Exception;
use App\Entity\Order;
use App\Entity\File;
use App\Command\Import\Trait\UpdateFieldsTrait;
use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\DoctrineProvider;

#[AsCommand(
    name: 'import:from-elab',
    description: 'import data from elab',
)]
class ImportFromElabCommand extends Command
{
    use UpdateFieldsTrait;

    private $output;

    private $pdo;

    private $baseFilePath;

    private $clientId;

    private $exportClientId;

    private $entityManager;

    use ImportExportTrait;

    public function __construct(EntityManagerInterface $entityManager, private PositionManager $positionManager)
    {
        $this->entityManager = $entityManager;

        $this->baseFilePath = realpath(__DIR__ . '/../../../../files/');
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import from elab db export.')
            ->setHelp('This command allows you to connect to a MySQL database using provided credentials.')
            ->addArgument('clientId', InputArgument::REQUIRED, 'Client ID')
            ->addArgument('exportClientId', InputArgument::REQUIRED, 'Export Client ID')
            ->addArgument('host', InputArgument::REQUIRED, 'Database host')
            ->addArgument('dbName', InputArgument::REQUIRED, 'Database name')
            ->addArgument('username', InputArgument::REQUIRED, 'Database username')
            ->addArgument('password', InputArgument::OPTIONAL, 'Database password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Start timer
        $startTime = microtime(true);

        /*

        Do this? Load from client data 22.3.2024. Toni

        $this->clientId = $clientId = $input->getArgument('clientId');

        $clients = require __DIR__ . '/../../config/getClients.php';

        $clientData = $clients[$clientId];
        $passwordEncoded = urlencode($clientData['password']);

        $host = $clientData['host'];
        $dbName = $input->getArgument('clientId');
        $username = $clientData['user'];
        $password = $passwordEncoded ?? '';
        */

        $this->clientId = $input->getArgument('clientId');
        $this->exportClientId = $input->getArgument('exportClientId');
        $host = $input->getArgument('host');
        $dbName = $input->getArgument('dbName');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password') ?? '';

        try {
            $this->pdo = $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            echo "<fg=green>Successfully connected to the database: $dbName</>";
        } catch (PDOException $e) {
            echo "<fg=red>Database connection failed: " . $e->getMessage() . "</>";
            return 1;
        }

        $this->output = $output;
        
        # add sampleTypes
        $sampleTypes = $this->getFromDb('sampleTypes');
        $this->writeSampleTypes($sampleTypes);
        $output->writeln('sampleTypes written');

        $sampleTypesMeta = $this->getFromDb('sampleTypes_meta');
        $this->writeSampleTypesMeta($sampleTypesMeta);
        $output->writeln('SampleType metas written');

        $sampleMetas = $this->getFromDb('sample_meta');
        $this->writeSampleCustomFieldValuesFromSampleMeta($sampleMetas);
        $output->writeln('Sample metas written');

        $groups = $this->getFromDb('groups');
        $this->writeGroups($groups);
        $output->writeln('groups written');

        // get users from sqlite database
        $users = $this->getFromDb('users');

        $this->writeUsers($users);
        $output->writeln('users written');

    
        $projects = $this->getFromDb('projects');
        $this->writeProjects($projects);
        $output->writeln('projects written');

        // get studies
        $studies = $this->getFromDb('studies');
        $this->writeStudies($studies);
        $output->writeln('studies written');

        # add catalogItems
        $catalogItems = $this->getFromDb('catalogItems');
        $this->writeCatalogItems($catalogItems);
        $output->writeln('catalogItems written');

        # add orders
        $orders = $this->getFromDb('orders');
        $this->writeOrders($orders);
        $output->writeln('orders written');

        # add experiments
        $experiments = $this->getFromDb('experiments');
        $this->writeExperiments($experiments);
        $output->writeln('experiments written');

        # add storageTypes
        $storageTypes = $this->getFromDb('storageTypes');
        $this->writeStorageTypes($storageTypes);
        $output->writeln('storageTypes written');

        # get protocol_steps
        $protocolVariables = $this->getFromDb('protocol_variables');
        $this->writeProtocolVariables($protocolVariables);
        $output->writeln('protocol_variables written');

        # get protocol_steps
        $protocolSteps = $this->getFromDb('protocol_steps');
        $this->writeProtocolFields($protocolSteps);
        $output->writeln('protocol_steps written');

        # add protocols
        $protocols = $this->getFromDb('protocols');
        $this->writeProtocols($protocols);
        # get protocol_variables
        $output->writeln('protocols written');

        # add storage
        $storage = $this->getFromDb('storage');
        $this->writeStorage($storage);
        $output->writeln('storage written');

        # add storageLayers
        $storageLayers = $this->getFromDb('storageLayers');
        $this->writeStorageLayers($storageLayers);
        $output->writeln('storageLayers written');

        # add samples
        $samples = $this->getFromDb('samples');
        # check if $samples last id is present in repository
        $present = $samples && $this->entityManager->getRepository(Sample::class)->findOneBy(['sampleID' => end($samples)['sampleID']]);
        if ($present) {
            $output->writeln('samples already written');
        } else {
            $output->writeln('writing samples');
            $this->writeSamples($samples);
            $output->writeln('samples written');
        }

        $sampleSeries = $this->getFromDb('sampleSeries');
        $output->writeln('writing sampleSeries');
        $this->writeSampleSeries($sampleSeries);
        $output->writeln('sampleSeries written');

        $files = $this->getFromDb('files');
        $output->writeln('writing files');
        $this->writeFiles($files);
        $output->writeln('files written');

        # remove (not optimized) $experimentSectionsContent = $this->getFromDb('experiment_sections_content');
        $experimentSectionFiles = $this->getFromDb('experiment_sections_files');
        $output->writeln('writing experiment section files');
        $this->writeExperimentSectionFiles($experimentSectionFiles);
        $output->writeln('experiment_sections_files written');

        $experimentSectionFiles = $this->getFromDb('experiment_sections_images');
        $output->writeln('writing experiment section images');
        $this->writeExperimentSectionFiles($experimentSectionFiles);
        $output->writeln('experiment_sections_images written');
    
        # add experiment_sections
        $experimentSections = $this->getFromDb('experiment_sections');

        # check if $experimentSections last id is present in repository
        $present = $this->entityManager->getRepository(Section::class)->findOneBy(['expJournalId' => end($experimentSections)['expJournalID']]);
        # if count less than as in repository
        $count = $this->entityManager->getRepository(Section::class)->count([]) >= count($experimentSections);
        if ($present && $count) {
            $output->writeln('experiment_sections already written');
        } else {
            # remove (not optimized) $experimentSectionsContent = $this->getFromDb('experiment_sections_content');
            $output->writeln('writing experiment sections');
            $this->writeExperimentSections($experimentSections);
            $output->writeln('experiment_sections written');
        }

        # build relationships between section and file based on expJournalId
        $output->writeln('building section file relationships');
        $this->buildSectionFileRelationships();
        $output->writeln('building section file relationships done');

        $output->writeln('updating storage positions');
        $this->updateStoragePositions();
        $output->writeln('updated storage positions');

        $output->writeln('updating protocol fields');
        $this->updateProtocolFields();
        $output->writeln('updated protocol fields');

        $endTime = microtime(true);

        // Calculate elapsed time
        $elapsedTime = $endTime - $startTime; // Time in seconds
        $hours = floor($elapsedTime / 3600);
        $minutes = floor(($elapsedTime / 60) % 60);
        $seconds = $elapsedTime % 60;

        // Output the elapsed time
        $output->writeln(sprintf("Elapsed Time: %02d:%02d:%02d", $hours, $minutes, $seconds));

        # echo done in color
        $output->writeln('<fg=green>done</>');

        return Command::SUCCESS;
    }

    public function buildSectionFileRelationships()
    {
        $batchSize = 100;
        $i = 0;
    
        $sectionCount = $this->entityManager->getRepository(Section::class)->count([]);
        $batches = ceil($sectionCount / $batchSize);
    
        for ($batch = 0; $batch < $batches; $batch++) {
            $sections = $this->entityManager->getRepository(Section::class)
                                            ->findBy([], null, $batchSize, $batch * $batchSize);
    
            foreach ($sections as $section) {
                $expJournalId = $section->getExpJournalId();
    
                // Load only relevant files
                $sectionFiles = $this->entityManager->getRepository(File::class)
                                                    ->findBy(['expJournalID' => $expJournalId]);
    
                if ($sectionFiles) {
                    foreach ($sectionFiles as $sectionFile) {
                        $section->addFile($sectionFile);
                        $sectionFile->setExperimentSection($section);
                        $this->entityManager->persist($section);
                        $this->entityManager->persist($sectionFile);
                    }
                }
    
                if (($i % $batchSize) === 0) {
                    $this->entityManager->flush();
                    echo '--flush--';
                }
    
                $i++;
                echo '.';
            }
    
            $this->entityManager->flush(); // Flush remaining changes
            $this->entityManager->clear();
        }
    }
    

    public function writeFiles($files)
    {

        /*
        dd('$files', $files);
          11 => array:13 [
            "fileID" => 103544
            "filename" => "9452_4E-BP1_CS.pdf"
            "userID" => 29633
            "size" => 108614
            "groupID" => 7072
            "folderID" => 102974
            "created" => "2019-05-07 08:37:18"
            "location" => "ELABJOURNAL"
            "extension" => "pdf"
            "fileType" => "Portable Document"
            "icon" => "/media/images/elab_icons/48/file-pdf.png"
            "fullFilePath" => "/root/remoteProjects/files/clientId/1/fileId/103544/452_4E-BP1_CS.pd"
            "filePath" => "clientId/1/fileId/103544"
        ]

        */
        $i = 0;
        foreach($files as $fileArr) {
            
            # check if exists in entityManager
            $fileExists = $this->entityManager->getRepository(File::class)->findOneBy(['experimentFileID' => $fileArr['fileID']]);
            if ($fileExists) {
                echo '-';
                continue;
            }
            $file = new File();
            $file->setMeta($fileArr);
            $file->setFilename($fileArr['filename']);
            $file->setFilepath($fileArr['filePath']);
            $file->setFilesize($fileArr['size']);
            $file->setFullFilePath($fileArr['fullFilePath']);
            $file->setFiletype($fileArr['fileType']);
            // get file type from extension
            $fileType = $fileArr['extension'];
            $file->setFiletype($fileType);

            // persist
            $this->entityManager->persist($file);
            echo '.';

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }

    }

    public function writeExperimentSectionFiles($experimentSectionFiles)
    {

        $i = 0;
        foreach($experimentSectionFiles as $experimentSectionFile) {
            /*
            1^ "$experimentSectionFiles[0]"
                2^ array:14 [
                "experimentFileID" => 2564082
                "origin" => "S3"
                "parentExperimentFileID" => 0
                "userID" => 28945
                "expJournalID" => 4071768
                "experimentID" => 396423
                "storedName" => "20190516125614_H3legjgD2B"
                "realName" => "20190516-AH_21_AA-EPZ and Insulin.xlsx"
                "fullFilePath" => "/root/remoteProjects/files/clientId/1/experiment_sections/4071768/files/2564082_20190516-AH_21_AA-EPZ_and_Insulin.xlsx"
                "filePath" => "/clientId/1/experiment_sections/4071768/files/2564082_20190516-AH_21_AA-EPZ_and_Insulin.xlsx"
                "stored" => "2019-05-16 12:56:14"
                "SHA256Hash" => "42E6FB54C730AF2715FC5BB50B366F5AF2ED00EA628D57E8D5D3280556C3A4BE"
                "certificateHash" => "8A2F3AF51E9D089A7066D2D521B504DB8786B311BEA946D4686D49F648C793F4"
                "fileSize" => 13193
                ]
                */
            # check if exists in entityManager
            $fileExists = $this->entityManager->getRepository(File::class)->findOneBy(['experimentFileID' => $experimentSectionFile['experimentFileID']]);
            if ($fileExists) {
                echo '-';
                continue;
            }
            $file = new File();
            $file->setMeta($experimentSectionFile);
            $file->setExpJournalID($experimentSectionFile['expJournalID']);
            $file->setExperimentFileID($experimentSectionFile['experimentFileID']);
            $file->setFilename($experimentSectionFile['realName']);
            $file->setFilepath($experimentSectionFile['filePath']);
            $file->setFilesize($experimentSectionFile['fileSize']);
            $file->setFullFilePath($experimentSectionFile['fullFilePath']);
            // get file type from extension
            $fileType = pathinfo($experimentSectionFile['realName'], PATHINFO_EXTENSION);
            $file->setFiletype($fileType);

            $experimentSectionId = $experimentSectionFile['expJournalID'];
            $experimentSection = $this->entityManager->getRepository(Section::class)->findOneBy(['expJournalId' => $experimentSectionId]);

            !$experimentSection || $file->setExperimentSection($experimentSection);

            $experimentId = $experimentSectionFile['experimentID'];
            $experiment = $this->entityManager->getRepository(Experiment::class)->findOneBy(['experimentID' => $experimentId]);

            !$experiment || $file->setExperiment($experiment);

            // persist
            $this->entityManager->persist($file);
            echo '.';
            $i = 0;

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
        }

    }

    public function writeSampleTypesMeta($sampleTypesMeta)
    {
        $i = 0;
        // get sampleType by sampleTypeID from repository
        foreach($sampleTypesMeta as $sampleTypeMeta) {
            $sampleType = $this->entityManager->getRepository(SampleType::class)->findOneBy(['sampleTypeID' => $sampleTypeMeta['sampleTypeID']]);
            if ($sampleType) {
                $i++;
                $sampleType->setMeta($sampleTypeMeta);
                $this->entityManager->persist($sampleType);

                if (($i % 100) === 0) { // Assuming $i is your loop counter
                    $this->entityManager->flush();
                    $this->entityManager->clear(); // Detaches all objects from Doctrine
                }
            }
        }
    }

    public function writeSampleCustomFieldValuesFromSampleMeta($sampleMetas)
    {

        /*
1123 => array:12 [
    "id" => null
    "sampleID" => 14262132
    "archived" => ""
    "sampleMetaID" => 131363409
    "truncated" => ""
    "sampleDataType" => "COMBO"
    "samples" => "[]"
    "files" => "[]"
    "sampleTypeMetaDefaultsIDs" => "[384713]"
    "sampleTypeMetaID" => 174359
    "key" => "Supplier"
    "value" => "Not Specified"
  ]
1124 => array:12 [
    "id" => null
    "sampleID" => 14262132
    "archived" => ""
    "sampleMetaID" => 131363412
    "truncated" => ""
    "sampleDataType" => "DATE"
    "samples" => "[]"
    "files" => "[]"
    "sampleTypeMetaDefaultsIDs" => "[]"
    "sampleTypeMetaID" => 174356
    "key" => "Dated ordered"
    "value" => "2022-10-12"
  ]

1134 => array:12 [
    "id" => null
    "sampleID" => 12629345
    "archived" => ""
    "sampleMetaID" => 116598413
    "truncated" => ""
    "sampleDataType" => "TEXT"
    "samples" => null
    "files" => null
    "sampleTypeMetaDefaultsIDs" => "[]"
    "sampleTypeMetaID" => 183105
    "key" => "Owner"
    "value" => ""
  ]

  9 => array:12 [
    "id" => null
    "sampleID" => 7874146
    "archived" => ""
    "sampleMetaID" => 68884285
    "truncated" => ""
    "sampleDataType" => "TEXT"
    "samples" => "[]"
    "files" => "[]"
    "sampleTypeMetaDefaultsIDs" => "[]"
    "sampleTypeMetaID" => 185804
    "key" => "Concentration"
    "value" => "1 µM"
  ]

3 => array:12 [
    "id" => null
    "sampleID" => 8410571
    "archived" => ""
    "sampleMetaID" => 75499004
    "truncated" => ""
    "sampleDataType" => "FILE"
    "samples" => null
    "files" => null
    "sampleTypeMetaDefaultsIDs" => "[]"
    "sampleTypeMetaID" => 174362
    "key" => "Reference"
    "value" => ""
  ]
0 => array:12 [
    "id" => null
    "sampleID" => 8410571
    "archived" => ""
    "sampleMetaID" => 75498995
    "truncated" => ""
    "sampleDataType" => "TEXTAREA"
    "samples" => "[]"
    "files" => "[]"
    "sampleTypeMetaDefaultsIDs" => "[]"
    "sampleTypeMetaID" => 174353
    "key" => "Oligo sequence"
    "value" => "ACCCTGAGATGGTAGAGGGTCTC"
  ]

        */
        //dump('TODO $sampleMetas', $sampleMetas);
    }

    public function updateStoragePositions()
    {
        $sampleRepository = $this->entityManager->getRepository(Sample::class);
        $qb = $sampleRepository->createQueryBuilder('s');
    
        $iterableResult = $qb->getQuery()->iterate();
    
        foreach ($iterableResult as $row) {
            $sample = $row[0]; // Get the first element of the array which is the Sample entity
    
            try {
                echo '.';
                $this->positionManager->updatePositionTakenFromSample($sample);
                $this->entityManager->detach($sample); // Detach the entity to free up memory
            } catch (\Exception $e) {
                $this->output->writeln('Error updating position for sample ID ' . $sample->getId() . ': ' . $e->getMessage());
                // Optionally, flush and clear periodically to manage memory and synchronization with the database
            }
        }

        $this->entityManager->clear();
    }
    
    public function writeSampleSeries($data)
    {
        $i = 0;
        // Fetch all names from the database at once
        $names = array_column($data, 'name'); // Assuming $rows is the array you're looping over
        $existingSampleSeries = $this->entityManager->getRepository(SampleSeries::class)->findBy(['name' => $names]);

        // Convert the result to a set for efficient lookups
        $existingSampleSeriesNames = [];
        foreach ($existingSampleSeries as $sampleSeries) {
            $existingSampleSeriesNames[$sampleSeries->getName()] = true;
        }

        // Now in your loop, you can check the $existingSampleSeriesNames array instead of querying the database
        foreach ($data as $row) {
            if (isset($existingSampleSeriesNames[$row['name']])) {
                continue;
            }
            
            $sampleSeries = new SampleSeries();
            $sampleSeries->setName($row['name']);
            $sampleSeries->setBarcode($row['barcode']);
            # foreach $sampleIDs, get Sample from repository and add to $sampleSeries
            $sampleIDs = json_decode($row['sampleIDs'], true);
            if ($sampleIDs) {
                foreach ($sampleIDs as $sampleID) {
                    $sample = $this->entityManager->getRepository(Sample::class)->findOneBy(['sampleID' => $sampleID]);
                    if ($sample) {
                        $sampleSeries->addSample($sample);
                    }
                }
            }

            # get user from email by $row['userID'] in $this->getFromDb('users')
            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if ($userData) {
                $userData = array_values($userData);
                $userData = $userData[0];
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
                if ($user) {
                    $sampleSeries->setUser($user);
                }
            }

            $meta = [];
            $meta['seriesID'] = $row['seriesID'];
            $meta['userID'] = $row['userID'];
            $meta['name'] = $row['name'];
            $meta['barcode'] = $row['barcode'];
            $meta['created'] = $row['created'];
            # name
            $meta['name'] = $row['name'];
            #threshold
            $meta['threshold'] = $row['threshold'];
            # sampleIDs
            $meta['sampleIDs'] = $row['sampleIDs'];
            $sampleSeries->setMeta($meta);
            
            # persist
            $this->entityManager->persist($sampleSeries);

            echo '.';

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }
    }

    public function writeStorageLayers($data)
    {
        $i = 0;

        foreach($data as $row) {
            
            # check if exists in entityManager
            $storageLayerExists = $this->entityManager->getRepository(Storage::class)->findOneBy(['storageLayerId' => $row['storageLayerID']]);
            if ($storageLayerExists) {
                continue;
            }
            /*
            1^ "$row"
                2^ array:14 [
                "storageLayerID" => 546377
                "userID" => 28945
                "created" => "2019-04-25T14:40:14Z"
                "storageID" => 27578
                "barcode" => "008000000546377"
                "isGrid" => ""
                "parentStorageLayerID" => 0
                "position" => 0
                "transposed" => ""
                "dimension" => "{"rows":{"numbering":"NUMERIC","count":1},"columns":{"numbering":"NUMERIC","count":1}}"
                "storageLayerDefinitionID" => 85355
                "name" => "KTh - Lab 1 - Fridge 1"
                "icon" => "fridge"
                "maxSize" => 0
                ]
            */
            $storageLayer = new Storage();
            $storageLayer->setStorageLayerId($row['storageLayerID']);
            $storageLayer->setName($row['name']);
            $storageLayer->setBarcode($row['barcode']);
            //$storageLayer->setPosition($row['position']);
            $storageLayer->setDimensions($row['dimension']);
            //$storageLayer->setIsGrid($row['isGrid']);
            //$storageLayer->setTransposed($row['transposed']);
            //$storageLayer->setMaxSize($row['maxSize']);
            # get Storage from $row['parentStorageLayerID']
            $parentStorageLayer = $this->entityManager->getRepository(Storage::class)->findOneBy(['storageLayerId' => $row['parentStorageLayerID']]);
            if ($parentStorageLayer) {
                $storageLayer->setParent($parentStorageLayer);
            }

            # add position taken
            $position = $row['position'];

            $meta = [];
            $meta['storageLayerID'] = $row['storageLayerID'];
            $meta['userID'] = $row['userID'];
            $meta['created'] = $row['created'];
            $meta['storageID'] = $row['storageID'];
            $meta['barcode'] = $row['barcode'];
            $meta['isGrid'] = $row['isGrid'];
            $meta['parentStorageLayerID'] = $row['parentStorageLayerID'];
            $meta['position'] = $row['position'];
            $meta['transposed'] = $row['transposed'];
            $meta['dimension'] = $row['dimension'];
            $meta['storageLayerDefinitionID'] = $row['storageLayerDefinitionID'];
            $meta['name'] = $row['name'];
            $meta['icon'] = $row['icon'];
            $meta['maxSize'] = $row['maxSize'];
            $storageLayer->setMeta($meta);

            # set $gridType based on isGrid
            if ($row['isGrid']) {
                $gridType = 'grid';
            } else {
                $gridType = null;
            }

            # persist
            $this->entityManager->persist($storageLayer);

            echo '.';

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }
    }

    public function writeStorage($data)
    {
        // Fetch all storageLayerIDs from the database at once
        $storageLayerIds = array_column($data, 'storageLayerID');
        $existingStorages = $this->entityManager->getRepository(Storage::class)->findBy(['storageLayerId' => $storageLayerIds]);
    
        // Convert the result to a set for efficient lookups
        $existingStorageIds = [];
        foreach ($existingStorages as $storage) {
            $existingStorageIds[$storage->getStorageLayerId()] = true;
        }

        $i = 0;
    
        // Now in your loop, you can check the $existingStorageIds array instead of querying the database
        foreach ($data as $row) {
            if (isset($existingStorageIds[$row['storageLayerID']])) {
                continue;
            }

            /*	
            ^ array:23 [
                "storageLayerID" => 546377
                "storageTypeID" => 3
                "groupID" => 7072
                "userID" => 28945
                "name" => "KTh - Lab 1 - Fridge 1"
                "deviceType" => "STORAGE"
                "deviceTypeID" => 3
                "storageType" => "{"storageTypeID":3,"groupID":0,"userID":0,"name":"Refrigerator","deviceType":"STORAGE"}"
                "deviceTypeName" => "Refrigerator"
                "barcode" => "008000000546377"
                "status" => "Available"
                "storageID" => 27578
                "instituteID" => 2113
                "department" => "Institute of Biochemistry"
                "address" => ""
                "building" => ""
                "floor" => "3 OG"
                "room" => "L.03.010"
                "notes" => ""
                "updated" => "2023-07-14T13:01:32Z"
                "hasPlanner" => null
                "hasValidation" => null
                "hideFromBrowser" => null
                ]
                */
            $storage = new Storage();
            $storage->setStorageLayerId($row['storageLayerID']);
            $storage->setName($row['name']);
            $storage->setBarcode($row['barcode']);
            

            # get storage type by name $row['storageType']
            $storageType = $this->entityManager->getRepository(StorageType::class)->findOneBy(['name' => $row['storageType']]);
            if ($storageType) {
                $storage->setStorageType($storageType);
            }

            # get user from email by $meta['userID'] in $this->getFromDb('users')
            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if ($userData) {
                $userData = array_values($userData);
                $userData = $userData[0];
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
                if ($user) {
                    $storage->setUser($user);
                }
            }

            $meta = [];
            $meta['storageLayerID'] = $row['storageLayerID'];
            $meta['storageTypeID'] = $row['storageTypeID'];
            $meta['groupID'] = $row['groupID'];
            $meta['userID'] = $row['userID'];
            $meta['name'] = $row['name'];
            $meta['deviceType'] = $row['deviceType'];
            $meta['deviceTypeID'] = $row['deviceTypeID'];
            $meta['storageType'] = $row['storageType'];
            $meta['deviceTypeName'] = $row['deviceTypeName'];
            $meta['barcode'] = $row['barcode'];
            $meta['status'] = $row['status'];
            $meta['storageID'] = $row['storageID'];
            $meta['instituteID'] = $row['instituteID'];
            $meta['department'] = $row['department'];
            $meta['address'] = $row['address'];
            $meta['building'] = $row['building'];
            $meta['floor'] = $row['floor'];
            $meta['room'] = $row['room'];

            $storage->setDepartment($row['department']);
            $storage->setAddress($row['address']);
            $storage->setBuilding($row['building']);
            $storage->setFloor($row['floor']);
            $storage->setRoom($row['room']);

            $meta['notes'] = $row['notes'];
            $meta['updated'] = $row['updated'];
            $meta['hasPlanner'] = $row['hasPlanner'];
            $meta['hasValidation'] = $row['hasValidation'];
            $meta['hideFromBrowser'] = $row['hideFromBrowser'];

            $storage->setMeta($meta);
            
            # persist
            $this->entityManager->persist($storage);

            echo '.';

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }
    }

    public function writeSamples($data)
    {
        $i = 0;

        foreach($data as $row) {
            
            # check if exists in entityManager
            $sampleExists = $this->entityManager->getRepository(Sample::class)->findOneBy(['sampleID' => $row['sampleID']]);

            if ($sampleExists) {
                echo '-';
                continue;
            }

            /*
            1^ "$row"
                2^ array:18 [
                "sampleID" => 12491078
                "owner" => "Anna-Sophia Egger"
                "archived" => ""
                "meta" => "[]"
                "created" => "2022-03-23T12:45:41Z"
                "userID" => 42348
                "creatorID" => 42348
                "storageLayerID" => 898478
                "position" => 37
                "seriesID" => 545114
                "barcode" => "005000012491078"
                "sampleType" => "{"sampleTypeID":53096,"userID":34364,"groupID":7072,"name":"MS sample","backgroundColor":"666","foregroundColor":"FFF","showSectionsInTabs":false}"
                "sampleTypeID" => 53096
                "checkedOut" => ""
                "parentSampleID" => 0
                "name" => "ARE02_BC_nonpolar_ 1"
                "description" => ""
                "note" => ""
            ]
            */
            $sample = new Sample();
            $sample->setSampleID($row['sampleID']);
            $sample->setName($row['name']);
            $sample->setDescription($row['description']);
            $sample->setBarcode($row['barcode']);
            //$sample->setMeta($row['meta']);
            $sample->setUpdatedAt(new \DateTime($row['created']));
            $sample->setPosition($row['position']);
            # check if ?App\Entity\SampleType exists by name $row['sampleType']
            # if exists, set sampleType
            # if not exists, create new SampleType and set sampleType
            $sampleType = json_decode($row['sampleType'], true);
            if ($sampleType) {
                $sampleTypeExists = $this->entityManager->getRepository(SampleType::class)->findOneBy(['name' => $sampleType['name']]);
                if ($sampleTypeExists) {
                    $sample->setSampleType($sampleTypeExists);
                } else {
                    $newSampleType = new SampleType();
                    $newSampleType->setName($sampleType['name']);
                    $newSampleType->setBgColor($sampleType['backgroundColor']);
                    $newSampleType->setFgColor($sampleType['foregroundColor']);
    
                    $this->entityManager->persist($newSampleType);
                    $sample->setSampleType($newSampleType);
                }
            }
            
            # find sampleType by name
            $sampleType = $this->entityManager->getRepository(SampleType::class)->findOneBy(['sampleTypeID' => $row['sampleTypeID']]);
            if ($sampleType) {
                $sample->setSampleType($sampleType);
            }

            $meta = [];
            $meta['sampleID'] = $row['sampleID'];
            $meta['owner'] = $row['owner'];
            $meta['archived'] = $row['archived'];
            $meta['created'] = $row['created'];
            $meta['userID'] = $row['userID'];
            $meta['creatorID'] = $row['creatorID'];
            $meta['storageLayerID'] = $row['storageLayerID'];
            $meta['position'] = $row['position'];
            $meta['seriesID'] = $row['seriesID'];
            $meta['barcode'] = $row['barcode'];
            $meta['sampleType'] = $row['sampleType'];
            $meta['sampleTypeID'] = $row['sampleTypeID'];
            $meta['checkedOut'] = $row['checkedOut'];
            $meta['parentSampleID'] = $row['parentSampleID'];
            $meta['name'] = $row['name'];
            $meta['description'] = $row['description'];
            $meta['note'] = $row['note'];
            $sample->setMeta($meta);

            # get user from email by $meta['userID'] in $this->getFromDb('users')
            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if ($userData) {
                $userData = array_values($userData);
                $userData = $userData[0];
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
                if ($user) {
                    $sample->setUser($user);
                }
            }

            # if parentSampleID exists, get parentSample from repository
            if ($row['parentSampleID']) {
                $parentSample = $this->entityManager->getRepository(Sample::class)->findOneBy(['sampleID' => $row['parentSampleID']]);
                if ($parentSample) {
                    $sample->setParent($parentSample);
                }
            }
            
            # if storageLayerID exists, get storage from repository
            if ($row['storageLayerID']) {
                $storage = $this->entityManager->getRepository(Storage::class)->findOneBy(['storageLayerId' => $row['storageLayerID']]);
                if ($storage) {
                    $sample->setStorage($storage);
                }
            }
            
            # persist
            $this->entityManager->persist($sample);

            echo '.';

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }
    }

    public function writeExperimentSections($data)
    {
        // Fetch all expJournalIDs from the database at once
        $expJournalIds = array_column($data, 'expJournalID'); // Assuming $rows is the array you're looping over
        $existingSections = $this->entityManager->getRepository(Section::class)->findBy(['expJournalId' => $expJournalIds]);

        // Convert the result to a set for efficient lookups
        $existingSectionIds = [];
        foreach ($existingSections as $section) {
            $existingSectionIds[$section->getExpJournalId()] = true;
        }

        $i = 0;
        $filesNotFoundCount = 0;
        $directoriesNotFoundCount = 0;

        foreach($data as $row) {
            # check if exists in entityManager
            if (isset($existingSectionIds[$row['expJournalID']])) {
                echo '-';
                continue;
            }
            
            /*
            ^ array:17 [
                "generated_ID" => 1
                "expJournalID" => 4050063
                "experimentID" => 394218
                "userID" => 28945
                "lastEditUserID" => 29687
                "created" => "2019-05-10T06:21:13Z"
                "lastEditDate" => "2019-05-16T11:18:01Z"
                "sectionType" => "PARAGRAPH"
                "sectionOrder" => null
                "sectionHeader" => "Aim"
                "sectionDate" => "2019-05-10T06:21:09Z"
                "firstName" => "Alexander"
                "lastName" => "Heberle"
                "orderColumn" => 0
                "lastEditUserFirstName" => "Melanie"
                "lastEditUserLastName" => "Brunner"
                "deleted" => ""
                ]
            */
            $experimentSection = new Section();
            $experimentSection->setExpJournalId($row['expJournalID']);
            $experimentSection->setName($row['sectionHeader']);
            $createdAt = new \DateTime();
            $experimentSection->setCreatedAt($createdAt);
            
            $experimentID = $row['experimentID'];

            $experiment = $this->entityManager->getRepository(Experiment::class)->findOneBy(['experimentID' => $experimentID]);
            $experimentSection->setExperiment($experiment);

            if (!$experiment) {
                dump("This is file: " . __FILE__ . "\nThis is line number " . __LINE__);
                dump('experiment not found');
                continue;
            }

            // Check if realpath() returned false, which means the path could not be resolved
            if ($this->baseFilePath === false) {
                // Handle the error, e.g., the base path does not exist
                throw new \Exception('Base path does not exist.');
            }

            # get from getSingleValue method $experimentSectionsContent by sqlite single query for id
            # expJournalID that matches $experimentSectionContent['experimentSectionId']
            $experimentSectionContent = $this->getSingleValue('experiment_sections_content', 'experimentSectionId', $row['expJournalID']);

            # get first element of array if array
            if (is_array($experimentSectionContent)) {
                $experimentSectionContent = array_values($experimentSectionContent);
                $experimentSectionContent = isset($experimentSectionContent[0]) ? $experimentSectionContent[0] : null;
            }

            if ($row['sectionType'] == 'PARAGRAPH' || $row['sectionType'] == 'COMMENT' || $row['sectionType'] == 'DATATABLE') {
                if ($row['sectionType'] == 'COMMENT') {
                    $experimentSection->setType('comment');
                } elseif($row['sectionType'] == 'DATATABLE') {
                    $experimentSection->setType('dataTable');
                } else {
                    $experimentSection->setType('paragraph');
                }

                # set description from experiment_sections_content

                $meta = $experimentSectionContent['meta'];
                $experimentSection->setDescription($experimentSectionContent['contents']);
                if (isset($meta)) {
                    $experimentSection->setOrigMeta($meta);
                }

                #persist

            } elseif ($row['sectionType'] == 'PROCEDURE') {
                
                $experimentSection->setType('procedure');

                $experimentSection->setDescription($experimentSectionContent['contents']);
                $meta = $experimentSectionContent['meta'];
                !isset($meta) || $experimentSection->setOrigMeta($meta);
            } elseif ($row['sectionType'] == 'IMAGE') {

                $experimentID = $row['experimentID'];


                // Now, append the rest of the path. No need to worry about `..` or `.` anymore.
                $dirPath = $this->baseFilePath . '/clientId/' . $this->exportClientId . '/experiment_sections/' . $row['expJournalID'] . '/files';
                # create $baseDir

                $experimentSection->setType('image');

                // fetch File from repository
                $files = $this->entityManager->getRepository(File::class)->findOneBy(['expJournalID' => $row['expJournalID']]);

                if ($files) {
                    foreach ($files as $file) {
                        $experimentSection->addFile($file);
                        $file->setExperimentSection($experimentSection);
                        $this->entityManager->persist($file);
                    }
                } else {

                    // find File by $expJournalID
                    $file = $this->entityManager->getRepository(File::class)->findOneBy(['expJournalID' => $row['expJournalID']]);
                    
                    if (!$file) {

                        //dump('$dirPath', $dirPath);
                        //dump("This is file: " . __FILE__ . "\nThis is line number " . __LINE__);
                        
                        try {
                            $files = scandir($dirPath);
                            // if array has only two elements . and .. don't add to $files
                            if (is_array($files) && count($files) > 2) {
                                $files = array_diff($files, ['.', '..']);
                                
                                /*
                                dd('$files', $files);
                                2^ array:1 [
                                    2 => "2945699_191016_AH_23_AC-Cutting_scheme.PNG"
                                    ]
                                */
                                foreach ($files as $fileArr) {
                                    $file = new File();
                                    $file->setMeta($row);
                                    $file->setExpJournalID($row['expJournalID']);
                                    $file->setFilename($fileArr);
                                    $file->setFullFilePath($dirPath . '/' . $fileArr);
                                    $file->setFilesize(filesize($dirPath . '/' . $fileArr));
                                    $file->setFiletype(pathinfo($fileArr, PATHINFO_EXTENSION));
                                    # set experiment and section
                                    
                                    if ($experimentSection) {
                                        $file->setExperimentSection($experimentSection);
                                        $experimentSection->addFile($file);
                                    }
                                    $experiment = $this->entityManager->getRepository(Experiment::class)->findOneBy(['experimentID' => $experimentID]);
                                    $file->setExperiment($experiment);

                                    $this->entityManager->persist($file);
                                }
                            }
                        } catch (\Exception $e) {
                            dump("This is file: " . __FILE__ . "\nThis is line number " . __LINE__);
                            dump('$dirPath', $dirPath);
                            dump('$e->getMessage()', $e->getMessage());
                            $directoriesNotFoundCount++;
                            $filesNotFoundCount++;
                        }
                    } else {
                        $file->setExperimentSection($experimentSection);
                        $this->entityManager->persist($file);
                        $experimentSection->addFile($file);

                        $this->entityManager->persist($file);
                    }
                }
                
            } elseif ($row['sectionType'] == 'FILE') {
                
                $experimentID = $row['experimentID'];

                $dirPath = $this->baseFilePath . '/clientId/' . $this->exportClientId . '/experiment_sections/' . $row['expJournalID'] . '/files';

                if (!$dirPath) {
                    try {
                        $files = scandir($dirPath);
                    } catch (\Exception $e) {
                        $directoriesNotFoundCount++;
                        $filesNotFoundCount++;
                    }
                }

                $experimentSection->setType('file');
                $files = $this->entityManager->getRepository(File::class)->findOneBy(['experimentFileID' => $row['expJournalID']]);

                if ($files) {
                    foreach ($files as $file) {
                        $experimentSection->addFile($file);
                        $file->setExperimentSection($experimentSection);
                        $this->entityManager->persist($file);
                    }
                } else {
                    $filesNotFoundCount++;
                }
            } elseif ($row['sectionType'] == 'EXCEL') {
                
                $filenameId = $row['expJournalID'];
                $experimentID = $row['experimentID'];
                
                $dirPath = $this->baseFilePath . '/clientId/' . $this->exportClientId . '/experiment_sections/' . $row['expJournalID'] . '/files';

                if (!$dirPath) {
                    try {
                        $files = scandir($dirPath);
                    } catch (\Exception $e) {
                        $directoriesNotFoundCount++;
                        $filesNotFoundCount++;
                    }
                }

                $experimentSection->setType('excel');
                $files = $this->entityManager->getRepository(File::class)->findOneBy(['expJournalID' => $row['expJournalID']]);
                if ($files) {
                    foreach ($files as $file) {
                        $experimentSection->addFile($file);
                        $file->setExperimentSection($experimentSection);
                        $this->entityManager->persist($file);
                    }
                } else {
                    $filesNotFoundCount++;
                }
            } elseif ($row['sectionType'] == 'SAMPLESOUT' || $row['sectionType'] == 'SAMPLESIN') {

                if ($row['sectionType'] == 'SAMPLESOUT') {
                    $experimentSection->setType('samplesOut');
                } else {
                    $experimentSection->setType('samplesIn');
                }

                $experimentSectionsSamples = $this->getFromDb('experiment_sections_samples');
                # filter $experimentSectionsSamples by $row['expJournalID'] from $experimentSectionsSamples generator
                $experimentSectionsSamples = array_filter($experimentSectionsSamples, function($experimentSectionsSample) use ($row) {
                    return $experimentSectionsSample['experimentSectionId'] == $row['expJournalID'];
                });
                
                $experimentSectionsSamples = array_filter($experimentSectionsSamples, function($experimentSectionsSample) use ($row) {
                    return $experimentSectionsSample['experimentSectionId'] == $row['expJournalID'];
                });

                # from each $experimentSectionsSamples get sampleId and get samples from repository
                foreach ($experimentSectionsSamples as $experimentSectionsSample) {
                    
                    $sampleId = $experimentSectionsSample['sampleID'];

                    $sample = $this->entityManager->getRepository(Sample::class)->findOneBy(['sampleID' => $sampleId]);
                    if ($sample) {
                        $experimentSection->addSample($sample);
                    }
                }
            } else {
                dump("This is file: " . __FILE__ . "\nThis is line number " . __LINE__);
                dump('row[sectionType]');
                dump($row['sectionType']);
                dump('$experimentSectionContent');
                dump($experimentSectionContent);
                dump('writeExperimentSections $row');
                //$experimentSection->setType('table');
            }
            $meta = [];
            $meta['generated_ID'] = $row['generated_ID'];
            $meta['expJournalID'] = $row['expJournalID'];
            $meta['experimentID'] = $row['experimentID'];
            $meta['userID'] = $row['userID'];
            $meta['lastEditUserID'] = $row['lastEditUserID'];
            $meta['created'] = $row['created'];
            $meta['lastEditDate'] = $row['lastEditDate'];
            $meta['sectionType'] = $row['sectionType'];
            $meta['sectionOrder'] = $row['sectionOrder'];
            $meta['sectionHeader'] = $row['sectionHeader'];
            $meta['sectionDate'] = $row['sectionDate'];
            $meta['firstName'] = $row['firstName'];
            $meta['lastName'] = $row['lastName'];
            $meta['orderColumn'] = $row['orderColumn'];
            $meta['lastEditUserFirstName'] = $row['lastEditUserFirstName'];
            $meta['lastEditUserLastName'] = $row['lastEditUserLastName'];
            $meta['deleted'] = $row['deleted'];
            $experimentSection->setMeta($meta);

            # persist
            $this->entityManager->persist($experimentSection);

            // check if file exists: $filePath = $experimentSection->getFilePath()
            $files = $experimentSection->getFiles();

            foreach ($files as $file) {
                $filePath = $file->getFilePath();

                // Check if the file exists
                if (file_exists($filePath)) {
                    // The file exists
                    //echo "The file at {$filePath} exists.";
                } else {
                    if ($filePath) {
                        // The file does not exist
                        echo "NOTE: The file at {$filePath} does not exist.";
                    }
                }
            }


            echo '.';

            if (($i % 100) === 0) {
                echo '--flush--';
                $this->entityManager->flush(); // Executes the SQL
                $this->entityManager->clear(); // Detaches all entities from Doctrine
            }
        
            $i++;
        }
    }

    public function writeProtocolFields($data)
    {
        $i = 0;

        foreach($data as $row) {
            /*
            ^ array:7 [
                "stepID" => 63775
                "protVersionID" => 4
                "protID" => 29
                "name" => "Materials"
                "contents" => """
                    <TABLE>\r\n
                    <TBODY>\r\n
                    <TR>\r\n
                    <TD style="BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BORDER-TOP: medium none; BORDER-RIGHT: medium none">- Tryptone</TD>\r\n
                    <TD style="BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BORDER-TOP: medium none; BORDER-RIGHT: medium none">&nbsp;</TD></TR>\r\n
                    <TR>\r\n
                    <TD style="BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BORDER-TOP: medium none; BORDER-RIGHT: medium none">- Yeast extract</TD>\r\n
                    <TD style="BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BORDER-TOP: medium none; BORDER-RIGHT: medium none">&nbsp;</TD></TR>\r\n
                    <TR>\r\n
                    <TD style="BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BORDER-TOP: medium none; BORDER-RIGHT: medium none">- NaCl</TD>\r\n
                    <TD style="BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BORDER-TOP: medium none; BORDER-RIGHT: medium none">58.44 g mol<SUP>-1</SUP></TD></TR></TBODY></TABLE>
                    """
                "duration" => 0
                "orderColumn" => 1
                ]
                
            */
            $protocolField = new ProtocolField();
            $protocolField->setStepId($row['stepID']);
            $protocolField->setName($row['name']);
            $protocolField->setValue($row['contents']);
            $protocolField->setSortBy($row['orderColumn']);
            $meta['duration'] = $row['duration'];
            $meta['protVersionID'] = $row['protVersionID'];
            $meta['protID'] = $row['protID'];
            $meta['orderColumn'] = $row['orderColumn'];
            $protocolField->setMeta($meta);

            # check if exists in entityManager
            $protocolFieldExists = $this->entityManager->getRepository(ProtocolField::class)->findOneBy(['stepId' => $row['stepID']]);

            if ($protocolFieldExists) {
                continue;
            }

            echo '.';

            $this->entityManager->persist($protocolField);

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }

        return $data;
    }

    public function writeProtocolVariables($data)
    {
        foreach($data as $row) {
            $variable = new Variable();
            /*
            ^ array:11 [
                "varID" => 51
                "protVersionID" => 4
                "protID" => 29
                "name" => "VolLB"
                "description" => "Amount of LB medium to be prepared"
                "varType" => "combobox"
                "quantityID" => 23
                "unit" => "L"
                "contents" => """
                    1\n
                    2\n
                    5
                    """
                "dataSetId" => null
                "dataSetItems" => null
                ]
            */
            $variable->setVarID($row['varID']);
            $variable->setName($row['name']);
            $variable->setDescription($row['description']);
            $variable->setType($row['varType']);
            $variable->setUnit($row['unit']);
            $variable->setContents($row['contents']);
            $meta['varID'] = $row['varID'];
            $meta['protVersionID'] = $row['protVersionID'];
            $meta['protID'] = $row['protID'];
            $meta['quantityID'] = $row['quantityID'];
            $meta['dataSetId'] = $row['dataSetId'];
            $meta['dataSetItems'] = $row['dataSetItems'];
            $variable->setMeta($meta);

            # check if exists in entityManager
            $variableExists = $this->entityManager->getRepository(Variable::class)->findOneBy(['varID' => $row['varID']]);
            if ($variableExists) {
                continue;
            }

            $this->entityManager->persist($variable);

            echo '.';

            $this->entityManager->flush();
        }
    }

    public function writeProtocols($protocols)
    {
        $i = 0;
        // Implementation of the writeProtocols method goes here
        foreach ($protocols as $row) {

            // if exists by protId, continue
            $protocolExists = $this->entityManager->getRepository(Protocol::class)->findOneBy(['protId' => $row['protID']]);
            if ($protocolExists) {
                echo '-';
                continue;
            }

            $protocol = new Protocol();
            $protocol->setProtId($row['protID']);
            $protocol->setName($row['name']);
            $protocol->setDescription($row['description'] ?? null);
            $protocol->setCategory($row['category']);
            
            /*
            1^ "$row"
                2^ array:24 [
                "protID" => 111364
                "storageID" => 0
                "created" => "2023-11-07T15:04:12Z"
                "protVersionID" => 225748
                "latestVersionId" => 0
                "version" => 1
                "latestVersion" => 0
                "draft" => ""
                "isPublic" => 1
                "deleted" => ""
                "name" => "Promega Wizard® HMW DNA Extraction Kit - Whole Blood"
                "description" => ""
                "userID" => 67114
                "authorID" => 67114
                "author" => "Yue"
                "groupID" => 0
                "subgroupID" => 17146
                "categoryID" => 40
                "category" => "Experimental Procedures"
                "viewCount" => 1
                "rating" => 0
                "numSteps" => 19
                "groupShareCount" => 0
                "appViewURL" => "https://www.elabjournal.com/members/protocol/appView/?protID=111364&protVersionID=225748"
                ]
            */ 
            $meta['protID'] = $row['protID'];
            $meta['storageID'] = $row['storageID'];
            $meta['created'] = $row['created'];
            $meta['protVersionID'] = $row['protVersionID'];
            $meta['latestVersionId'] = $row['latestVersionId'];
            $meta['version'] = $row['version'];
            $meta['latestVersion'] = $row['latestVersion'];
            $meta['draft'] = $row['draft'];
            $meta['isPublic'] = $row['isPublic'];
            $meta['deleted'] = $row['deleted'];
            $meta['userID'] = $row['userID'];
            $meta['authorID'] = $row['authorID'];
            $meta['author'] = $row['author'];
            $meta['groupID'] = $row['groupID'];
            $meta['subgroupID'] = $row['subgroupID'];
            $meta['categoryID'] = $row['categoryID'];
            $meta['category'] = $row['category'];
            $meta['viewCount'] = $row['viewCount'];
            $meta['rating'] = $row['rating'];
            $meta['numSteps'] = $row['numSteps'];
            $meta['groupShareCount'] = $row['groupShareCount'];
            $meta['appViewURL'] = $row['appViewURL'];
            $protocol->setMeta($meta);

            // add user from authorID from $this->getFromDb('users') rows
            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if ($userData) {
                $userData = array_values($userData);
                $userData = $userData[0];
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData['email']]);
                if ($user) {
                    $protocol->setUser($user);
                }
            }

            // add protocol fields from repository by stepId from $this->getFromDb('protocol_steps') rows
            $protocolStepsData = array_filter($this->getFromDb('protocol_steps'), function($protocolStep) use ($row) {
                return $protocolStep['protID'] == $row['protID'];
            });

            // foreach $protocolStepsData key stepID get protocolField from repository
            foreach ($protocolStepsData as $protocolStepData) {
                $protocolField = $this->entityManager->getRepository(ProtocolField::class)->findOneBy(['stepId' => $protocolStepData['stepID']]);
                if ($protocolField) {
                    $protocolValue = $protocolField->getValue();
                    $pattern = '/{{var:id\((\d+)\)}}/';
                    $newProtocolValue = preg_replace($pattern, '{{ var(\'id:$1\') }}', $protocolValue);
                    $protocolField->setValue($newProtocolValue);
                    $this->entityManager->persist($protocolField);

                    $protocol->addField($protocolField);
                }
            }

            $this->entityManager->persist($protocol);

            echo '.';

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }
    }

    public function writeSampleTypes($data)
    {
        foreach ($data as $row) {

            // if exists, continue
            $sampleTypeExists = $this->entityManager->getRepository(SampleType::class)->findOneBy(['name' => $row['name']]);
            if ($sampleTypeExists) {
                continue;
            }

            $sampleType = new SampleType();
            $sampleType->setSampleTypeID($row['sampleTypeID']);
            $sampleType->setName($row['name']);
            $sampleType->setDescription($row['description']);

            /*
            ^ array:18 [
                "sampleTypeID" => 25532
                "userID" => 29051
                "groupID" => 7072
                "deleted" => ""
                "showSectionsInTabs" => ""
                "hasExpirationDate" => ""
                "defaultUnitShort" => "ml"
                "name" => "Antibody"
                "description" => ""
                "backgroundColor" => "60F"
                "foregroundColor" => "FFF"
                "quantityRequired" => ""
                "defaultQuantityType" => "Volume"
                "thresholdEnabled" => ""
                "defaultQuantityThreshold" => 0
                "defaultQuantityAmount" => 0
                "defaultThresholdAction" => "Nothing"
                "defaultUnit" => "MilliLiter"
            ]
            */
            # if color in $row['backgroundColor'] has only 3 characters, add 3 more characters so it can be like #c85151

            $sampleType->setBgColor('#' . $row['backgroundColor']);
            $sampleType->setFgColor('#' . $row['foregroundColor']);
            $sampleType->setQuantityType($row['defaultQuantityType']);
            $sampleType->setUnitType($row['defaultUnit']);


            echo '.';

            $this->entityManager->persist($sampleType);
        }

        $this->entityManager->flush();
    }

    public function writeStorageTypes($data)
    {
        /*
        ^ array:5 [
            "storageTypeID" => 556
            "groupID" => 0
            "userID" => 0
            "name" => "Microscope"
            "deviceType" => "EQUIPMENT"
            ]
        */
        foreach ($data as $row) {

            # if not in repository
            $storageTypeExists = $this->entityManager->getRepository(StorageType::class)->findOneBy(['name' => $row['name']]);
            if ($storageTypeExists) {
                continue;
            }
            
            $storageType = new StorageType();
            $storageType->setName($row['name']);
            $storageType->setShape($row['deviceType']);

            echo '.';
            $this->entityManager->persist($storageType);
        }

        $this->entityManager->flush();
    }

    public function writeExperiments($data)
    {
        $i = 0;

        foreach ($data as $row) {

            // if exists, continue
            $experimentExists = $this->entityManager->getRepository(Experiment::class)->findOneBy(['experimentID' => $row['experimentID']]);
            if ($experimentExists) {
                continue;
            }

            $experiment = new Experiment();
            $experiment->setExperimentID($row['experimentID']);
            $experiment->setName($row['name']);
            $experiment->setDescription($row['description']);

            // set createAt from eg. 2019-03-29T14:19:11Z from $row['created']
            $createdAt = new \DateTime($row['created']);
            $experiment->setCreatedAt($createdAt);
            //$experiment->setNumExperiments($row['numExperiments']);


            $studyId = $row['studyID'];
            $studyData = array_filter($this->getFromDb('studies'), function($study) use ($studyId) {
                return $study['studyID'] == $studyId;
            });

            $studyData = array_pop($studyData);

            # check if any array key exists in $studyData, 
            # and get the first one
            if ($studyData) {
                $study = $this->entityManager->getRepository(Study::class)->findOneBy(['name' => $studyData['name']]);
                if ($study) {
                    $experiment->setStudy($study);
                }
            }

            # get project from $row['projectID']
            $projectId = $row['projectID'];
            $projectData = array_filter($this->getFromDb('projects'), function($project) use ($projectId) {
                return $project['projectID'] == $projectId;
            });

            $projectData = array_pop($projectData);

            # check if any array key exists in $projectData,
            # and get the first one
            if ($projectData) {
                $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $projectData['longName']]);
                if ($project) {
                    $experiment->setProject($project);
                }
            }

            // create experimentStatus entity if it doesnt exist
            // if exists, json encode and set status to $row['experimentStatus']
            $experimentStatus = json_decode($row['experimentStatus'], true);

            if (isset($experimentStatus['status'])) {
                $experiment->setStatus($experimentStatus['status']);
            } else {
                $experiment->setStatus($row['experimentStatus']);
            }

            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if (isset($userData[0])) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData[0]['email']]);
                if ($user) {
                    //$experiment->addCollaborator($user);
                }
            }

            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if (isset($userData[0])) {
                $userData = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData[0]['email']]);
                if ($userData) {
                    $experiment->addResearcher($user);
                }
            }

            echo '.';

            $this->entityManager->persist($experiment);

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }
    }

    public function writeStudies($data)
    {
        $i = 0;
        
        foreach ($data as $row) {

            // if exists, continue
            $studyExists = $this->entityManager->getRepository(Study::class)->findOneBy(['name' => $row['name']]);
            if ($studyExists) {
                continue;
            }

            $study = new Study();
            $study->setName($row['name']);
            $study->setDescription($row['description']);

            // set createAt from eg. 2019-03-29T14:19:11Z from $row['created']
            $createdAt = new \DateTime($row['created']);
            $study->setCreatedAt($createdAt);

            // json_encode $row['studyStatus']
            // if $row['studyStatus'] is not json, set status to $row['studyStatus']
            if (json_decode($row['studyStatus'], true)) {
                $studyStatus = json_decode($row['studyStatus'], true);
                $study->setStatus($studyStatus['status']);
            } else {
                $study->setStatus($row['studyStatus']);
            }

            $projectId = $row['projectID'];
            $projectData = array_filter($this->getFromDb('projects'), function($project) use ($projectId) {
                return $project['projectID'] == $projectId;
            });
            if (isset($projectData[0])) {
                $projectData = array_values($projectData);
                $projectData = $projectData[0];
                $project = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $projectData['longName']]);
                if ($project) {
                    $study->setProject($project);
                }
            }
            
            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if (isset($userData[0])) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData[0]['email']]);
                if ($user) {
                    //$study->addCollaborator($user);
                }
            }

            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if (isset($userData[0])) {
                $userData = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData[0]['email']]);
                if ($userData) {
                    $study->setLeadResearcher($user);
                }
            }

            $this->entityManager->persist($study);

            echo '.';

            if (($i % 100) === 0) { // Assuming $i is your loop counter
                $this->entityManager->flush();
                $this->entityManager->clear(); // Detaches all objects from Doctrine
            }
            $i++;
        }
    }

    public function writeCatalogItems($data)
    {
        foreach ($data as $row) {
            $catalogItem = new CatalogItem();
            $catalogItem->setName($row['name']);
            $catalogItem->setPrice($row['price']);

            $meta = [];
            $meta['groupID'] = $row['groupID'];
            $meta['supplierID'] = $row['supplierID'];
            $meta['catalogNumber'] = $row['catalogNumber'];
            $meta['amount'] = $row['amount'];
            $meta['quantityType'] = $row['quantityType'];
            $meta['currency'] = $row['currency'];
            $meta['details'] = $row['details'];
            $meta['unitShort'] = $row['unitShort'];
            $meta['barcode'] = $row['barcode'];
            $meta['content'] = $row['content'];
            $meta['productImage'] = $row['productImage'];
            $catalogItem->setMeta($meta);

            $this->entityManager->persist($catalogItem);

            echo '.';
        }

        $this->entityManager->flush();
    }

    public function writeOrders($data)
    {
        foreach ($data as $row) {
            $order = new Order();
            $order->setOrderNumber($row['shoppingItemID'] . '-' . $row['userID']);
            $orderDate = new \DateTime($row['dateOrdered']);
            $order->setOrderDate($orderDate);
            $order->setStatus($row['status']);
            $order->setTotalAmount($row['amount']);

            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if (isset($userData[0])) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData[0]['email']]);
                if ($user) {
                    $order->setOrderedBy($user);
                }
            }

            $this->entityManager->persist($order);

            echo '.';
        }

        $this->entityManager->flush();
    }

    public function writeUsers($data)
    {
        foreach ($data as $row) {
            $user = new User();
            $user->setEmail($row['email']);
            $user->setFirstname($row['firstName']);
            $user->setLastname($row['lastName']);
            $user->setRoles([$row['role']]);
            $user->setPassword('password');

            // add group from group name by groupId from $this->getFromDb('groups') rows
            $groupData = array_filter($this->getFromDb('groups'), function($group) use ($row) {
                return $group['groupID'] == $row['groupId'];
            });

            if ($groupData) {
                $groupData = array_values($groupData);
                $groupData = $groupData[0];
                $group = $this->entityManager->getRepository(Group::class)->findOneBy(['name' => $groupData['name']]);
                if ($group) {
                    $user->addGroup($group);
                    $group->addUser($user);
                }
            }

            // if $user doesn't exist, persist
            $userExists = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $row['email']]);
            if (!$userExists) {
                $this->entityManager->persist($user);

                echo '.';
            } else {
                !isset($group) || $this->entityManager->persist($userExists);
            }
        }

        $this->entityManager->flush();
    }

    public function writeProjects($data)
    {
        foreach ($data as $row) {

            $projectExists = $this->entityManager->getRepository(Project::class)->findOneBy(['name' => $row['longName']]);
            if ($projectExists) {
                continue;
            }

            $project = new Project();
            $project->setName($row['longName'] ?? $row['name']);
            $project->setShortName($row['name']);
            $project->setDescription($row['description'] ?? '');

            // set createAt from eg. 2019-03-29T14:19:11Z from $row['created']
            $createdAt = new \DateTime($row['created']);
            $project->setCreatedAt($createdAt);

            $userData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['userID'];
            });

            if (isset($userData[0])) {
                $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userData[0]['email']]);
                if ($user) {
                    $project->addCollaborator($user);
                }
            }

            $creatorData = array_filter($this->getFromDb('users'), function($user) use ($row) {
                return $user['userID'] == $row['creatorID'];
            });

            if (isset($creatorData[0])) {
                $creatorUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $creatorData[0]['email']]);
                if ($creatorUser) {
                    //$project->setUser($user);
                }
            }

            // add group from group name by groupId from $this->getFromDb('groups') rows
            $groupData = array_filter($this->getFromDb('groups'), function($group) use ($row) {
                return $group['groupID'] == $row['groupID'];
            });

            if ($groupData) {
                $groupData = array_values($groupData);
                $groupData = $groupData[0];
                $group = $this->entityManager->getRepository(Group::class)->findOneBy(['name' => $groupData['name']]);
                if ($group) {
                    $project->setGroup($group);
                }
            }

            $this->entityManager->persist($project);

            echo '.';
        }

        $this->entityManager->flush();
    }

    public function writeGroups($data)
    {
        foreach ($data as $row) {
            $group = new Group();
            $group->setName($row['name']);
            $group->setDescription($row['description']);

            $groupExists = $this->entityManager->getRepository(Group::class)->findOneBy(['name' => $row['name']]);

            if (!$groupExists) {
                $this->entityManager->persist($group);
                echo '.';
            }
        }

        $this->entityManager->flush();
    }

    public function getFromDb($table)
    {
        try {
            // Assume $this->pdo has been previously initialized and connected to MySQL
    
            // Set the PDO error mode to exception
            $sql = "SELECT * FROM `" . $table . "`"; // Using backticks around table name
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $rows = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $row;
            }
            
            return $rows;
            
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public function getSingleValue($table, $id, $idValue)
    {
        try {
            // Assuming $this->pdo is already initialized and set up for MySQL connection as shown in your code snippet.
            $pdo = $this->pdo;
    
            // Prepare the SQL statement with placeholders to prevent SQL injection
            $stmt = $pdo->prepare("SELECT * FROM $table WHERE $id = :idValue");
    
            // Bind the value to the placeholder
            $stmt->bindParam(':idValue', $idValue);
    
            // Execute the query
            $stmt->execute();
    
            // Fetch the data
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $data;
    
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
