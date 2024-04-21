<?php

namespace App\Command\Backup;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

// create snapshot
class RestoreBackupCommand extends Command
{

    /** @var OutputInterface */
    private $output;

    /** @var InputInterface */
    private $input;

    private $host;
    private $database;
    private $username;
    private $password;
    private $restorePath;

    /** filesystem utility */
    private $fs;

    protected function configure()
    {
        $this->setName('backup:restore-backup')
            ->setDescription('Restore backup.')
            ->addArgument('host', InputArgument::REQUIRED)
            ->addArgument('database', InputArgument::REQUIRED)
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('restorePath', InputArgument::REQUIRED);
        // php api/bin/console backup:restore-backup 127.0.0.1 fb_owl_1_BAK root '' /home/toni/Documents/projects/feedback-owl-app/backup/fb_owl_1_2022-02-03_20:08:53.sql
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->host = $input->getArgument('host');
        $this->database = $input->getArgument('database');
        $this->username = $input->getArgument('username');
        $this->password = $input->getArgument('password');
        $this->restorePath = $input->getArgument('restorePath');
        $this->fs = new Filesystem();
        $this->output->writeln(sprintf('<comment>Restoring <fg=green>%s</fg=green> from <fg=green>%s</fg=green> </comment>', $this->database, $this->restorePath));
        $this->restoreDatabase();
        $output->writeln('<comment>All done.</comment>');

        return Command::SUCCESS;
    }

    private function restoreDatabase()
    {
        $cmd = sprintf('mysql -u %s --password=%s -h %s %s<%s',
            $this->username,
            $this->password,
            $this->host,
            $this->database,
            $this->restorePath,
        );

        if ($_ENV['APP_ENV'] === 'dev' && php_uname() === 'Windows NT DESKTOP-U0S1DMK 10.0 build 19044 (Windows 10) AMD64') {
            $cmd = 'C:/Users/toni/' . $cmd;
        }

        $result = $this->runCommand($cmd);

        if ($result['exit_status'] > 0) {
            throw new \Exception('Could not restore database: ' . var_export($result['output'], true));
        }

        return Command::SUCCESS;
    }

    /**
     * Runs a system command, returns the output
     *
     * @param $command
     * @param $streamOutput
     * @param $outputInterface mixed
     * @return array
     */
    protected function runCommand($command)
    {
        //$command .= " >&1";
        exec($command, $output, $exitStatus);
        return array(
            "output" => $output,
            "exit_status" => $exitStatus
        );
    }
}
