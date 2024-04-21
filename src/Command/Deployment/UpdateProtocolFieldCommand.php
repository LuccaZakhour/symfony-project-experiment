<?php

namespace App\Command\Deployment;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Protocol;
use App\Command\Import\Trait\UpdateFieldsTrait;

# this is already run in import:from-elab
class UpdateProtocolFieldCommand extends Command
{
    use UpdateFieldsTrait;
    protected static $defaultName = 'deployment:update-protocol-id';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Replace href old protId to working id in every ProtocolField->value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->updateProtocolFields();

        // flush
        $this->entityManager->flush();

        $output->writeln('All protocol fields href replaced successfully.');

        return Command::SUCCESS;
    }
}