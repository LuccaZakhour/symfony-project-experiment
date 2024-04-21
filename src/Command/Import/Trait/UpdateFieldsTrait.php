<?php

namespace App\Command\Import\Trait;

use App\Entity\Protocol;
use App\Entity\ProtocolField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait UpdateFieldsTrait {

    public function updateProtocolFields()
    {
        $protocolRepository = $this->entityManager->getRepository(Protocol::class);
        $batchSize = 100; // Adjust batch size as needed
        $totalProtocols = $protocolRepository->count([]);
        $batches = ceil($totalProtocols / $batchSize);

        $i = 0;
    
        for ($batch = 0; $batch < $batches; $batch++) {
            // Calculate offset for the current batch
            $offset = $batch * $batchSize;
            // Fetch a batch of protocols
            $protocols = $protocolRepository->findBy([], null, $batchSize, $offset);
        
            foreach ($protocols as $protocol) {
                $fields = $protocol->getFields();

                foreach ($fields as $field) {
                    $value = $field->getValue();

                    // This pattern matches 'javascript:Protocol.openProtocol('Transformation E. coli ',[integer])'
                    // and captures the integer in a group for manipulation.
                    $pattern = '/(javascript:Protocol\.openProtocol\(\'(.*?)\',)(\d+)(\))/';

                    $newValue = preg_replace_callback(
                        $pattern,
                        function ($matches) use ($protocolRepository, &$protocol) {
                            // $matches[3] is the captured integer (e.g., 141 or 124)
                            $protId = (int) $matches[3];
                            // $matches[2] captures the protocol name
                            $protName = $matches[2];
                    
                            // First attempt to find the protocol by its ID
                            $protocol = $protocolRepository->findOneBy(['protId' => $protId]);
                    
                            // If not found by ID, attempt to find by name with a LIKE query
                            if (null === $protocol) {
                                // Adjust the query to suit your ORM or database abstraction layer
                                // This is a general approach; specific implementation can vary
                                $protocol = $protocolRepository->createQueryBuilder('p')
                                    ->where('p.name LIKE :name')
                                    ->setParameter('name', '%' . $protName . '%')
                                    ->setMaxResults(1)
                                    ->getQuery()
                                    ->getOneOrNullResult();
                            }
                    
                            // Check again if $protocol is found
                            if (null === $protocol) {
                                // remove original value
                                return $matches[1] . $protId . $matches[4];
                            }
                    
                            // Replace the found ID/name with the protocol's actual ID in the match
                            return $matches[1] . $protocol->getId() . $matches[4];
                        },
                        $value
                    );

                    // Now $newValue contains the updated string. You can update the field's value here
                    // Assuming there's a method to set the updated value
                    $field->setValue($newValue);

                    // Replace the original $value with the updated $newValue if necessary
                    $this->entityManager->persist($field);

                    echo '.';

                    if (($i % 100) === 0) { // Assuming $i is your loop counter
                        $this->entityManager->flush();
                    }
                    $i++;
                }
            }
        }

        $this->entityManager->flush();
    }
}