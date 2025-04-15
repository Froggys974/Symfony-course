<?php

namespace App\Repository;

use App\Entity\WatchHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WatchHistory>
 */
class WatchHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WatchHistory::class);
    }

    public function getTendanceMedia(int $maxTendance)
    {
        $qb = $this->createQueryBuilder('wahi')
            ->select('medi.id AS media', 'SUM(wahi.numberOfViews) AS nbViews')
            ->innerJoin('wahi.media','medi')
            ->groupBy('medi.id')
            ->orderBy('SUM(wahi.numberOfViews)', 'ASC')
            ->setMaxResults($maxTendance);

        return $qb->getQuery()->getResult();


    }

    //    /**
    //     * @return WatchHistory[] Returns an array of WatchHistory objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?WatchHistory
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
