<?php

namespace App\Repository;

use App\Entity\SampleSeries;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SampleSeries>
 *
 * @method SampleSeries|null find($id, $lockMode = null, $lockVersion = null)
 * @method SampleSeries|null findOneBy(array $criteria, array $orderBy = null)
 * @method SampleSeries[]    findAll()
 * @method SampleSeries[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SampleSeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SampleSeries::class);
    }

//    /**
//     * @return SampleSeries[] Returns an array of SampleSeries objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SampleSeries
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
