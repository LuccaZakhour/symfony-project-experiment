<?php

namespace App\Service;

use App\Entity\Storage;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sample;

class PositionManager
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addPositionTaken(Storage $storage, $position): void
    {
        $positions = $storage->getPositionTaken() ?? [];
        if (!in_array($position, $positions)) {
            $positions[] = $position;
            $storage->setPositionTaken($positions);
            $this->entityManager->persist($storage);
            $this->entityManager->flush();
        }
    }

    public function removePositionTaken(Storage $storage, $position): void
    {
        $positions = $storage->getPositionTaken() ?? [];
        if (($key = array_search($position, $positions)) !== false) {
            unset($positions[$key]);
            $storage->setPositionTaken(array_values($positions)); // Re-index array
            $this->entityManager->persist($storage);
            $this->entityManager->flush();
        }
    }

    public function updatePositionTakenFromSample(Sample $sample): void
    {
        $storage = $sample->getStorage();
        if ($storage) {
            $position = $sample->getPosition();
            if ($position !== null) {
                $this->addPositionTaken($storage, $position);
            }
        }
    }
}
