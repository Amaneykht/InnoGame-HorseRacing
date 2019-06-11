<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\HorseInRace;
use App\Entity\Race;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method HorseInRace|null find($id, $lockMode = null, $lockVersion = null)
 * @method HorseInRace|null findOneBy(array $criteria, array $orderBy = null)
 * @method HorseInRace[]    findAll()
 * @method HorseInRace[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HorseInRaceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, HorseInRace::class);
    }

    /**
     * @return HorseInRace[] Returns an array of Horse objects
     */
    public function getHorsesInfoByRace(Race $race)
    {
      return $this->createQueryBuilder('h')
        ->andWhere('h.raceId = :val')
        ->setParameter('val', $race->getId())
        ->orderBy('h.position', 'ASC')
        ->setMaxResults(3)
        ->getQuery()
        ->getResult();
    }

    /**
     * @return HorseInRace[] Returns an array of Horse objects
     */
    public function findBestTimeWithHorseStats()
    {
      return $this->createQueryBuilder('h')
        ->orderBy('h.completedTime', 'ASC')
        ->innerJoin('h.race', 'r')
        ->andWhere('r.status = :val')
        ->setParameter('val', Race::COMPLETED_STATUS)
        ->setMaxResults(1)
        ->getQuery()
        ->getResult();
    }

    public function createTopThreeCriteria(): Criteria {
      return Criteria::create()
        ->orderBy(['position' => 'ASC'])
        ->setMaxResults(3);
    }
}
