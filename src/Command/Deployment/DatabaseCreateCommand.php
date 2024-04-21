<?php

namespace App\Command\Deployment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseCreateCommand extends Command
{
    protected static $defaultName = 'deployment:database:create';

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootDbPassword = $_ENV['DATABASE_PASSWORD'];

        $conn = new \PDO('mysql:host=' . $_ENV['DATABASE_HOST'], $_ENV['DATABASE_ROOT'], $rootDbPassword);
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $databaseName = $_ENV['DATABASE_PREFIX'] . $input->getArgument('id');
        $databasePassword = sha1(uniqid());

        $conn->exec("DROP DATABASE IF EXISTS `$databaseName`;");
        $conn->exec("DROP USER IF EXISTS '$databaseName'@'%';");
        $conn->exec("CREATE DATABASE `$databaseName`;");
        $conn->exec("CREATE USER IF NOT EXISTS '$databaseName'@'%' IDENTIFIED BY '$databasePassword';");

        $rootCmd = "GRANT ALL PRIVILEGES ON `$databaseName`.* TO '" . $_ENV['DATABASE_ROOT'] . "'@'%';";
        $conn->exec($rootCmd);
        $conn->exec('FLUSH PRIVILEGES;');

        $grantCmd = "GRANT ALL PRIVILEGES ON $databaseName.* TO '$databaseName'@'%';";
        $conn->exec($grantCmd);
        $conn->exec('FLUSH PRIVILEGES;');

        $output->writeln(json_encode([
            'host' => $_ENV['DATABASE_HOST'],
            'name' => $databaseName,
            'password' => $databasePassword,
        ]));

        return Command::SUCCESS;
    }
}
