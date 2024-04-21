<?php

namespace App\Command\Deployment;

use App\Entity\Variable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\ProtocolField;

# this is already run in import:from-elab
class UpdateVariableFormatCommand extends Command
{
    protected static $defaultName = 'deployment:update-variable-format';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Updates the format of all variables.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = $this->entityManager->getRepository(ProtocolField::class);
        $protocolFields = $repository->findAll();

        foreach ($protocolFields as $protocolField) {
            $contents = $protocolField->getValue();

            //Replace {{var:id(51)}} with {{ var('id:51') }}
            $updatedContents = preg_replace('/{{var:id\((\d+)\)}}/', "{{ var('id:$1') }}", $contents);

            $protocolField->setValue($updatedContents);

            $this->entityManager->persist($protocolField);

            echo '.';
        }

        $this->entityManager->flush();

        $output->writeln('Updated the format of all variables.');

        return Command::SUCCESS;
    }
}