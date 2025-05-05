<?php

namespace App\Repository;

use App\Entity\Business;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Business>
 */
class BusinessRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Business::class);
    }

    public function updateById($business)  {
        return $this->createQueryBuilder('q')
            ->update()
            ->set("q.name", ":name")
            ->set("q.streetNumber", ":streetNumber")
            ->set("q.streetName", ":streetName")
            ->set("q.CityName", ":CityName")
            ->set("q.phone", ":phone")
            ->set("q.description", ":description")
            // ->set("q.statistic", ":statistic")
            // ->set("q.footprintCarbon", ":footprintCarbon")
            ->where("q.id = :id")
            ->setParameter("name", $business["name"])
            ->setParameter("streetNumber", $business["streetNumber"])
            ->setParameter("streetName", $business["streetName"])
            ->setParameter("CityName", $business["CityName"])
            ->setParameter("phone", $business["phone"])
            ->setParameter("description", $business["description"])
            // ->setParameter("statistic", $business["statistic"])
            // ->setParameter("footprintCarbon", $business["footprintCarbon"])
            ->setParameter("id", $business["id"])
            ->getQuery()
            ->execute();
    }

    //    /**
    //     * @return Business[] Returns an array of Business objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Business
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function countUnvalidatedBusinesses(): int
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.validated = 0') 
            ->getQuery()
            ->getSingleScalarResult();
    }

    
    public function countvalidatedBusinesses(): int
    {
        return $this->createQueryBuilder('b')
            ->select('COUNT(b.id)')
            ->where('b.validated = 1') 
            ->getQuery()
            ->getSingleScalarResult();
    }
}
