<?php

namespace App\Command\Deployment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstanceCreateCommand extends Command
{
    protected static $defaultName = 'deployment:instance:create';
    protected static string $path = __DIR__ . '/../../..';

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED);

        $this->addArgument('adminEmail', InputArgument::REQUIRED);
        $this->addArgument('adminPassword', InputArgument::REQUIRED);

        $this->addArgument('user', InputArgument::REQUIRED);
        $this->addArgument('password', InputArgument::REQUIRED);
        $this->addArgument('host', InputArgument::REQUIRED);
        $this->addArgument('demoEnd', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        #putenv("DATABASE_URL=mysql://{$input->getArgument('user')}:{$input->getArgument('password')}@{$input->getArgument('host')}:3306/{$input->getArgument('user')}");

        $this->writeConfig($input);
        $output->writeln('config updated');

        $this->setupDatabase($input);
        $output->writeln('databases updated');

        $this->createAdmin($input);
        $output->writeln('admin user added');

        $this->runInitScripts($input);
        $output->writeln('runInitScripts runned');

        return Command::SUCCESS;
    }

    private function writeConfig(InputInterface $input)
    {
        $dbUser = $input->getArgument('user');

        $clients = (require self::$path . '/config/getClients.php');

        $clients[$input->getArgument('id')] = [
            'host' => $_ENV['DATABASE_HOST'],
            'database' => $_ENV['DATABASE_PREFIX'] . $input->getArgument('id'),
            'user' => $dbUser,
            'password' => $input->getArgument('password'),
            'host' => $input->getArgument('host'),
            'demoEnd' => $input->getArgument('demo_end'),
        ];

        file_put_contents(self::$path . '/config/.clients', serialize($clients));
    }

    private function setupDatabase($input)
    {
        system($_ENV['PHP_EXEC'] . ' "' . self::$path . '/bin/migrate.php" ');
        system($_ENV['PHP_EXEC'] . ' "' . self::$path . '/bin/update.php" ');

        // putenv DATABASE_URL
        $dbUrl = "mysql://{$input->getArgument('user')}:{$input->getArgument('password')}@{$_ENV['DATABASE_HOST']}:3306/{$input->getArgument('user')}";
        putenv("DATABASE_URL={$dbUrl}");
    }

    private function createAdmin(InputInterface $input)
    {
        $commandStr = $_ENV['PHP_EXEC'] .
            ' "' . self::$path . '/bin/add-user.php" '
            . $input->getArgument('id') . ' ' . $input->getArgument('adminEmail') . ' ' . $input->getArgument('adminPassword');

        system($commandStr);
    }

    private function runInitScripts(InputInterface $input)
    {
        $commandStr = $_ENV['PHP_EXEC'] .
            ' "' . self::$path . '/bin/consoleForClient" ' . $input->getArgument('id')
            . ' assets:install -n';
        $commandStr2 = $_ENV['PHP_EXEC'] .
            ' "' . self::$path . '/bin/consoleForClient" ' . $input->getArgument('id')
            . ' cache:clear -n &>/dev/null';
        $commandStr3 = $_ENV['PHP_EXEC'] .
            ' "' . self::$path . '/bin/consoleForClient" ' . $input->getArgument('id')
            . ' deployment:update-storage-positions -n &>/dev/null';
            
        system($commandStr);
        system($commandStr2);
        system($commandStr3);
    }
}
