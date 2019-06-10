<?php

namespace App\Repository;

use App\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Race|null find($id, $lockMode = null, $lockVersion = null)
 * @method Race|null findOneBy(array $criteria, array $orderBy = null)
 * @method Race[]    findAll()
 * @method Race[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Race::class);
    }

    /**
    * @return Race[] Returns an array of Race objects
    */
    public function findInProgressRaces()
    {
      return $this->createQueryBuilder('r')
        ->andWhere('r.status = :val')
        ->setParameter('val', Race::IN_PROGRESS_STATUS)
        ->orderBy('r.id', 'ASC')
        ->getQuery()
        ->getResult();
    }

    /**
     * @return Race[] Returns an array of Race objects
     */
    public function findLastFiveRaces()
    {
      return $this->createQueryBuilder('r')
        ->andWhere('r.status = :val')
        ->setParameter('val', Race::COMPLETED_STATUS)
        ->orderBy('r.createdDateTime', 'DESC')
        ->setMaxResults(5)
        ->getQuery()
        ->getResult();
    }
}
