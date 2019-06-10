<?php

namespace App\Tests\Utils;

use App\Entity\Horse;
use App\Entity\HorseInRace;
use App\Entity\Race;
use App\Repository\HorseInRaceRepository;
use App\Repository\HorseRepository;
use App\Repository\RaceRepository;
use App\Utils\Validator;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class RaceSimulationServiceTest extends TestCase
{
    private $raceRepository;
    private $horseInRaceRepository;
    private $horseRepository;

    private $objectManager;

    public function __construct()
    {
      // mock the repositories
      $this->raceRepository = $this->createMock(RaceRepository::class);
      $this->horseInRaceRepository = $this->createMock(HorseInRaceRepository::class);
      $this->horseRepository = $this->createMock(HorseRepository::class);


      // mock the EntityManager to return the mock of the repository
      $this->objectManager = $this->createMock(ObjectManager::class);
    }

  /**
   * @param Race $race
   *
   * @return HorseInRace[]
   */
  public function testGenerateRandomHorsesPerRace(Race $race) : array
  {
    $horses = [];
    //get all horses that didn't have races in the same day
    $availableHorses = $this->horseRepository->findAvailableHorses();

    $this->horseRepository->expects($this->any())
      ->method('findAvailableHorses')
      ->willReturn([
        (new Horse())
          ->setId(1)
          ->setSpeed(10)
          ->setStrength(9)
          ->setEndurance(9)
        ,
        (new Horse())
          ->setId(2)
          ->setSpeed(10)
          ->setStrength(9)
          ->setEndurance(8)
        ,
        (new Horse())
          ->setId(3)
          ->setSpeed(8)
          ->setStrength(9)
          ->setEndurance(7)
        ,
        (new Horse())
          ->setId(4)
          ->setSpeed(7)
          ->setStrength(6)
          ->setEndurance(5)
        ,
        (new Horse())
          ->setId(5)
          ->setSpeed(5)
          ->setStrength(6)
          ->setEndurance(9)
        ,
        (new Horse())
          ->setId(6)
          ->setSpeed(4)
          ->setStrength(7)
          ->setEndurance(3)
        ,
        (new Horse())
          ->setId(7)
          ->setSpeed(3)
          ->setStrength(4)
          ->setEndurance(6)
        ,
        (new Horse())
          ->setId(8)
          ->setSpeed(8)
          ->setStrength(4)
          ->setEndurance(4)
        ,
        (new Horse())
          ->setId(9)
          ->setSpeed(2)
          ->setStrength(6)
          ->setEndurance(5)
        ,
        (new Horse())
          ->setId(10)
          ->setSpeed(5)
          ->setStrength(5)
          ->setEndurance(5)
        ,
      ]);

    //randomly select 8 from them
    $indexes = [0,1,3,4,5,7,8,9];
    for ($i = 0; $i < $indexes; $i++)
    {
      $horseInRace = new HorseInRace();
      $horseInRace->setHorse($availableHorses[$i]);
      $horseInRace->setRace($race);
      $horses[] = $horseInRace;
    }

    return $horses;
  }

  /**
   *
   * Calculate the distance the horses covered based on their stats and the time
   *
   * @param HorseInRace $horse
   * @param int $numberOfSeconds
   *
   * @return float
   */
  public function calculateDistanceCovered(HorseInRace $horse, int $numberOfSeconds): float
  {
    if ($horse->getDistanceCovered() >= Race::DEFAULT_DISTANCE) {
      return $horse->getDistanceCovered();
    }

    $speed = Horse::BASE_SPEED + $horse->getHorse()->getSpeed();

    // calculate Endurance Distance
    $enduranceDistance = $horse->getHorse()->getEndurance() * 100;

    // calculate Slow Effect by Jockey
    $slowEffect = (HorseInRace::JOCKEY_SLOW_FACTOR - $horse->getHorse()->getStrength() * HorseInRace::JOCKEY_SLOW_PERCENTAGE) * $numberOfSeconds;

    // calculate distance covered
    return ($horse->getDistanceCovered() >= $enduranceDistance)
      ? $horse->getDistanceCovered() + $speed * $numberOfSeconds - $slowEffect
      : $horse->getDistanceCovered() + $speed * $numberOfSeconds;
  }
}
