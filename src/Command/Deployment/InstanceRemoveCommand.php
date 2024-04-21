<?php

namespace App\Command\Deployment;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstanceRemoveCommand extends Command
{
    protected static $defaultName = 'deployment:instance:remove';
    protected static string $path = __DIR__ . '/../../..';

    protected function configure()
    {
        $this->addArgument('id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $clients = (require self::$path . '/config/getClients.php');

        unset($clients[$input->getArgument('id')]);

        file_put_contents(self::$path . '/config/.clients', serialize($clients));

        return Command::SUCCESS;
    }
}
