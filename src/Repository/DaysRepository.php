<?php

namespace App\Repository;

use App\Entity\Days;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Days>
 */
class DaysRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Days::class);
    }

    public function updateById($day)  {
        return $this->createQueryBuilder('q')
            ->update()
            ->set("q.day_name", ":day_name")
            ->where("q.id = :id")
            ->setParameter("id", $day["id"])
            ->setParameter("day_name", $day["day_name"])
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return Days[] Returns an array of Days objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Days
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
