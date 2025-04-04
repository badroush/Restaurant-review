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
    public function findBestRated(int $limit = 8, ?int $userId = null)
{
    return $this->createQueryBuilder('r')
        ->select('r.id, r.name, r.adresse, r.cuiqinz, r.imageurl, r.description')
        ->addSelect('AVG(e.rating) as averageRating')
        ->addSelect('COUNT(DISTINCT l.id) as likesCount')
        ->addSelect('COUNT(DISTINCT CASE WHEN e.commentaire IS NOT NULL THEN e.id ELSE 0 END) as commentCount')
        ->addSelect('(CASE WHEN COUNT(lu.id) > 0 THEN true ELSE false END) as isLikedByUser')
        ->leftJoin('r.evaluations', 'e')
        ->leftJoin('r.likes', 'l')
        ->leftJoin('r.likes', 'lu', 'WITH', 'lu.user = :userId')
        ->setParameter('userId', $userId)
        ->groupBy('r.id')
        ->orderBy('averageRating', 'DESC')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
}
// src/Repository/RestaurantRepository.php


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