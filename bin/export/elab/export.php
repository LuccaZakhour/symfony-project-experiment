<?php


require_once __DIR__ . '/../../../vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;

(new SingleCommandApplication())
    ->addArgument('databaseName', InputArgument::OPTIONAL, 'database name')  
    ->setCode(function (InputInterface $input, OutputInterface $output) {

        if ($input->getArgument('databaseName')) {
            $databaseName = $input->getArgument('databaseName');
        } else {
            $databaseName = false;
        }
        // Start timer
        $startTime = microtime(true);

        // variables are defined in config.php
        require_once __DIR__ . '/config.php';

        // SQLite end
        require_once __DIR__ . '/lib/helper_functions.php';

        $i = 0;

        $tasks = [
            'sampleTypes_meta',
            'samples_viewcolumns',
            'samplesAndSeries',
            'groups',
            'projects',
            'studies',
            'experiments',
            'experiments_templates',
            'notes',
            'measurementUnits',
            'protocols',
            'protocols_categories',
            'quantityType',
            'sampleTypes',
            'samples',
            'orders',
            'catalogItems',
            'storage',
            'storageTypes',
            'storageLayers',
            'sampleSeries',
            'systemsettings',
            'files',
            'experiment_sections',
            'experiment_logs',
            'experiment_collaborators',
            'experiment_sections_content',
            'experiment_sections_html',
            'experiment_sections_samples',
            'experiment_sections_excel',
            'experiment_sections_files',
            'experiment_signatureWorkflow',
            'experiment_sections_images',
        ];

        $taskDependents = [
            'experiment_sections' => ['experiment_sections_content', 'experiment_sections_html', 
                'experiment_sections_samples', 'experiment_sections_excel', 'experiment_sections_files', 'experiment_sections_images'], 
            'experiments' => ['experiment_sections', 'experiment_collaborators', 'experiment_logs', 'experiment_signatureWorkflow']
        ];

        $maxConcurrentProcesses = 5; // Adjust based on your server's capacity, 5 take about 5GB of memory, 7 takes 6.9GB and 100% CPU
        $activeProcesses = [];

        $phpBinary = PHP_BINARY;
        $completedTasks = []; // Track completed tasks

        // Initialize variables
        $allSuccess = true; // Track overall success

        // Function to manage and start tasks with dependency checking
        function manageTasks(array &$tasks, array &$taskDependents, array &$completedTasks, array &$activeProcesses, $maxConcurrentProcesses, $phpBinary) {
            foreach ($tasks as $taskKey => $task) {
                if (count($activeProcesses) >= $maxConcurrentProcesses) {
                    // Maximum number of processes reached; break to wait for some to complete
                    break;
                }

                // Check if dependencies are met
                $dependenciesMet = true;
                if (isset($taskDependents[$task])) {
                    foreach ($taskDependents[$task] as $dependency) {
                        if (!in_array($dependency, $completedTasks)) {
                            $dependenciesMet = false;
                            break;
                        }
                    }
                }

                if ($dependenciesMet) {
                    // All dependencies met, start the process
                    echo "Starting task: $task\n";
                    $process = new Process([$phpBinary, __DIR__ . '/partial/' . $task . '.php']);
                    $process->setTimeout(null);
                    $process->start();
                    $activeProcesses[$task] = $process;
                    unset($tasks[$taskKey]); // Remove from pending tasks
                }
            }
        }

        // Main loop
        while (!empty($tasks) || !empty($activeProcesses)) {
            // Attempt to start new tasks if there's capacity
            manageTasks($tasks, $taskDependents, $completedTasks, $activeProcesses, $maxConcurrentProcesses, $phpBinary);

            // Check active processes
            foreach ($activeProcesses as $task => $process) {
                if (!$process->isRunning()) {
                    // Process completed
                    echo "Task completed: $task\n";
                    $completedTasks[] = $task; // Mark as completed
                    unset($activeProcesses[$task]); // Remove from active processes

                    // Process completion check (success or error)
                    if (!$process->isSuccessful()) {
                        $allSuccess = false;
                        // Handle error
                        echo "Error in task $task: " . $process->getErrorOutput() . "\n";
                    }
                }
            }

            // Optional: Sleep to reduce CPU load
            usleep(100000); // 0.1 second
        }

        // Final output based on $allSuccess
        if ($allSuccess) {
            echo "All processes completed successfully.\n";
        } else {
            echo "One or more processes failed.\n";
        }


        while (!empty($activeProcesses)) {
            waitForProcessCompletion($activeProcesses, $completedTasks);
        }

        if ($allSuccess) {
            echo "All processes completed successfully.\n";
        } else {
            echo "One or more processes failed.\n";
        }

        $allSuccess = true;


    
        $endTime = microtime(true);

        // Final wrap-up or cleanup tasks
        $output->writeln("All tasks completed.");

        $output->writeln('');

        // Calculate elapsed time
        $elapsedTime = $endTime - $startTime; // Time in seconds
        $hours = floor($elapsedTime / 3600);
        $minutes = floor(($elapsedTime / 60) % 60);
        $seconds = $elapsedTime % 60;

        // Output the elapsed time
        $output->writeln(sprintf("Elapsed Time: %02d:%02d:%02d", $hours, $minutes, $seconds));

        # echo done in color
        $output->writeln('<fg=green>done</>');

        logMessage("Script finished.", __FILE__, __LINE__);
        logMessage("Memory usage: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB" , __FILE__, __LINE__);

        return Command::SUCCESS;
    })
    ->run();