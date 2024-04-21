<?php

namespace App\Command\Backup;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

// create snapshot
class CreateBackupCommand extends Command
{

    /** @var OutputInterface */
    private $output;

    /** @var InputInterface */
    private $input;

    private $host;
    private $database;
    private $username;
    private $password;
    private $dirpath;

    /** filesystem utility */
    private $fs;

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('backup:create-backup')
            ->setDescription('Create backup.')
            ->addArgument('host', InputArgument::REQUIRED)
            ->addArgument('database', InputArgument::REQUIRED)
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
            ->addArgument('dirpath', InputArgument::REQUIRED);
        // php api/bin/console backup:create-backup localhost fb_owl_1 root '' /home/toni/Documents/projects/feedback-owl-app/backup
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
        $this->dirpath = $input->getArgument('dirpath');

        $this->fs = new Filesystem() ;
        $this->output->writeln(sprintf('<comment>Dumping <fg=green>%s</fg=green> to <fg=green>%s</fg=green> </comment>', $this->database, $this->dirpath ));
        $this->createDirectoryIfRequired();
        $this->dumpDatabase();
        $output->writeln('<comment>All done.</comment>');

        return Command::SUCCESS;
    }

    private function createDirectoryIfRequired() {
        if (!$this->fs->exists($this->dirpath)){
            $this->fs->mkdir(dirname($this->dirpath));
        }
    }

    private function dumpDatabase()
    {
        touch($this->dirpath);

        $cmd = 'mysqldump -u ' . $this->username . ' --password=' . $this->password . ' -h ' . $this->host . ' ' . $this->database . ' > ' . $this->dirpath;

        if ($_ENV['APP_ENV'] === 'dev' && php_uname() === 'Windows NT DESKTOP-U0S1DMK 10.0 build 19044 (Windows 10) AMD64') {
            $cmd = 'C:/Users/toni/' . $cmd;
        }

        $result = $this->runCommand($cmd);

        if($result['exit_status'] > 0) {
            throw new \Exception('Could not dump database: ' . var_export($result['output'], true));
        }
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
        //$command .=" >&1";

        exec($command, $output, $exit_status);

        return array(
            "output"      => $output,
            "exit_status" => $exit_status
        );
    }
}
