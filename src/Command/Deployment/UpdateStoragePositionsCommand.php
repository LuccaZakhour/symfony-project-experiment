<?php

namespace App\Command\Deployment;

use App\Service\PositionManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sample;

class UpdateStoragePositionsCommand extends Command
{
    protected static $defaultName = 'deployment:update-storage-positions';

    private $positionManager;
    private $entityManager;

    public function __construct(PositionManager $positionManager, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->positionManager = $positionManager;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates the positions taken in storages based on all Sample entities.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sampleRepository = $this->entityManager->getRepository(Sample::class);
        $samples = $sampleRepository->findAll();

        foreach ($samples as $sample) {
            try {
                echo '.';
                $this->positionManager->updatePositionTakenFromSample($sample);
            } catch (\Exception $e) {
                $output->writeln('Error updating position for sample ID ' . $sample->getId() . ': ' . $e->getMessage());
                continue;
            }
        }

        $output->writeln('All positions updated successfully.');

        return Command::SUCCESS;
    }
}
