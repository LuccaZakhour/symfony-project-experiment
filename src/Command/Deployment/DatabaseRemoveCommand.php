<?php

namespace App\Command\Deployment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseRemoveCommand extends Command
{
    protected static $defaultName = 'deployment:database:remove';

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /*
        if ($_ENV['APP_ENV'] === 'dev' && $_ENV["DATABASE_HOST"] === 'localhost') {
            $rootDbPassword = '';
        } else {
            $rootDbPassword = $_ENV['DATABASE_PASSWORD'];
        }

        $conn = new \PDO('mysql:host=' . $_ENV['DATABASE_HOST'], $_ENV['DATABASE_ROOT'], $rootDbPassword);
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $databaseName = $_ENV['DATABASE_PREFIX'] . $input->getArgument('id');
        */
        // get database details from client id (id is the client id)
        $clients = (require __DIR__ . '/../../../config/getClients.php');
        $client = $clients[$input->getArgument('id')];
        //dd('$client', $client);

        $databaseName = $client['database'];

        $conn = new \PDO('mysql:host=' . $client['host'], $client['user'], $client['password']);
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $conn->exec("DROP DATABASE IF EXISTS `$databaseName`;");
        $conn->exec("DROP USER IF EXISTS `$databaseName`;");
        $conn->exec('FLUSH PRIVILEGES;');

        $conn->exec("DELETE * FROM user");

        return Command::SUCCESS;
    }
}
