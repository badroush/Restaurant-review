<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function countByRestaurant($restaurant)
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.restaurant = :restaurant')
            ->setParameter('restaurant', $restaurant)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
