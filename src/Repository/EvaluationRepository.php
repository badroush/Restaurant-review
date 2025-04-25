<?php

namespace App\Repository;

use App\Entity\Evaluation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evaluation>
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }
    /**
     * Récupère les restaurants triés par la meilleure note
     */
    // public function findBestRated(int $limit = 5)
    // {
    //     return $this->createQueryBuilder('e')
    //     ->select('r.id, r.name,r.adresse,r.cuiqinz,r.imageurl,r.description,AVG(e.rating) as averageRating') // Sélectionner l'ID, le nom du restaurant et la moyenne des notes
    //     ->join('e.restaurant', 'r') // Joindre la table Restaurant via la relation
    //     ->groupBy('r.id') // Grouper par restaurant
    //     ->orderBy('averageRating', 'DESC') // Trier par la moyenne des notes (du plus haut au plus bas)
    //     ->setMaxResults($limit) // Limiter le nombre de résultats
    //     ->getQuery()
    //     ->getResult();
    // }

    //    /**
    //     * @return Evaluation[] Returns an array of Evaluation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Evaluation
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // src/Repository/EvaluationRepository.php

public function countByRestaurant($restaurant)
{
    return $this->createQueryBuilder('e')
                ->select('COUNT(e.id)')
                ->where('e.restaurant = :restaurant')
                ->setParameter('restaurant', $restaurant)
                ->getQuery()
                ->getSingleScalarResult();
}

}