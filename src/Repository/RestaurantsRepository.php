<?php

namespace App\Repository;

use App\Entity\Restaurants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurants>
 */
class RestaurantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurants::class);
    }
    public function findBestRated(int $limit = 8)
    {
        return $this->createQueryBuilder('r')
        ->select('r.id, r.name, r.adresse, r.cuiqinz, r.imageurl, r.description, AVG(e.rating) as averageRating') // Sélectionner les champs nécessaires
        ->leftJoin('App\Entity\Evaluation', 'e', 'WITH', 'e.restaurant = r.id') // Joindre la table Evaluation via la relation
        ->groupBy('r.id') // Grouper par restaurant
        ->orderBy('averageRating', 'DESC') // Trier par la moyenne des notes (du plus haut au plus bas)
        ->setMaxResults($limit) // Limiter le nombre de résultats
        ->getQuery()
        ->getResult();
    }

    //    /**
    //     * @return Restaurants[] Returns an array of Restaurants objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Restaurants
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
