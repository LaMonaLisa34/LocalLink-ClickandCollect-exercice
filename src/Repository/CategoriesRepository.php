<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categories>
 */
class CategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categories::class);
    }

    //    /**
    //     * @return Categories[] Returns an array of Categories objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Categories
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByQuery(string $query): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.business', 'b') // Jointure avec les commerces
            ->addSelect('b')             // Inclure les commerces dans les résultats
            ->leftJoin('b.products', 'p') // Jointure avec les produits
            ->addSelect('p')             // Produit
            ->where('c.category LIKE :query')  // Rechercher dans les catégories
            ->orWhere('b.name LIKE :query')    // Rechercher dans le nom des commerces
            ->orWhere('b.streetName LIKE :query') // Rechercher dans le nom de rue
            ->orWhere('b.CityName LIKE :query')   // Rechercher dans le nom de la ville
            ->orWhere('p.name LIKE :query')    // Rechercher dans les noms de produits
            ->setParameter('query', '%' . $query . '%') // Recherche partielle
            ->setMaxResults(50); // Limiter les résultats
    
        return $qb->getQuery()->getResult();
    }
}
