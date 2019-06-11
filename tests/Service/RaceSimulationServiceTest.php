<?php

namespace App\Tests\Utils;

use App\Entity\Horse;
use App\Entity\HorseInRace;
use App\Entity\Race;
use App\Repository\HorseInRaceRepository;
use App\Repository\HorseRepository;
use App\Repository\RaceRepository;
use App\Service\RaceSimulationService;
use App\Utils\HorseInRaceSorter;
use App\Utils\RaceValidator;
use App\Utils\Validator;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RaceSimulationServiceTest extends KernelTestCase
{
    private $raceRepository;
    private $horseInRaceRepository;
    private $horseRepository;

    private $objectManager;
    private $raceSimService;


    function setUp()
    {
      $kernel = self::bootKernel();

      $entityManager = $kernel->getContainer()
        ->get('doctrine')
        ->getManager();

      // mock the repositories
      $this->raceRepository = $this->createMock(RaceRepository::class);
      $this->objectManager = $this->createMock(ObjectManager::class);
      $this->horseInRaceRepository = $this->createMock(HorseInRaceRepository::class);
      $this->horseRepository = $this->createMock(HorseRepository::class);

      $raceValidator = new RaceValidator($this->raceRepository);
      $sorter = new HorseInRaceSorter();

      $this->raceSimService = new RaceSimulationService($entityManager,
          $raceValidator,
          $this->horseInRaceRepository,
          $this->raceRepository,
          $this->horseRepository,
          $sorter
      );
    }

    public function testUpdateHorseInfoPerRaceByTime(): array
    {
      $this->raceRepository->expects($this->any())
        ->method('findInProgressRaces')
        ->willReturn([
          (new Race())
            ->setId(10)
            ->setStatus(1)
            ->setCreatedDateTime(new \DateTime('2019-06-10 19:00:00'))
            ->setHorses([
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(1)
                  ->setSpeed(10)
                  ->setStrength(9)
                  ->setEndurance(9)),
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(4)
                  ->setSpeed(7)
                  ->setStrength(6)
                  ->setEndurance(5)),
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(5)
                  ->setSpeed(5)
                  ->setStrength(6)
                  ->setEndurance(9)),
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(7)
                  ->setSpeed(3)
                  ->setStrength(4)
                  ->setEndurance(6)),
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(3)
                  ->setSpeed(8)
                  ->setStrength(9)
                  ->setEndurance(7)),
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(10)
                  ->setSpeed(5)
                  ->setStrength(5)
                  ->setEndurance(5)),
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(8)
                  ->setSpeed(8)
                  ->setStrength(4)
                  ->setEndurance(4)),
              (new HorseInRace())
                ->setDistanceCovered(10)
                ->setCompletedTime(10)
                ->setPosition(1)
                ->setHorse((new Horse())
                  ->setId(6)
                  ->setSpeed(4)
                  ->setStrength(7)
                  ->setEndurance(3))
            ])
        ]);

      $horses = $this->raceSimService->updateHorseInfoPerRaceByTime(10);

      $this->assertEquals(10.0, $horses[0]->getDistanceCovered());
      $this->assertEquals(10.0, $horses[1]->getDistanceCovered());
      $this->assertEquals(10.0, $horses[2]->getDistanceCovered());
      $this->assertEquals(10.0, $horses[3]->getDistanceCovered());
      $this->assertEquals(10.0, $horses[4]->getDistanceCovered());
      $this->assertEquals(10.0, $horses[5]->getDistanceCovered());
      $this->assertEquals(10.0, $horses[6]->getDistanceCovered());
      $this->assertEquals(10.0, $horses[7]->getDistanceCovered());

      $this->assertEquals(1, $horses[0]->getPosition());
      $this->assertEquals(2, $horses[1]->getPosition());
      $this->assertEquals(3, $horses[2]->getPosition());
      $this->assertEquals(4, $horses[3]->getPosition());
      $this->assertEquals(5, $horses[4]->getPosition());
      $this->assertEquals(6, $horses[5]->getPosition());
      $this->assertEquals(7, $horses[6]->getPosition());
      $this->assertEquals(8, $horses[7]->getPosition());

      $this->assertEquals(10.0, $horses[0]->getCompletedTime());
      $this->assertEquals(10.0, $horses[1]->getCompletedTime());
      $this->assertEquals(10.0, $horses[2]->getCompletedTime());
      $this->assertEquals(10.0, $horses[3]->getCompletedTime());
      $this->assertEquals(10.0, $horses[4]->getCompletedTime());
      $this->assertEquals(10.0, $horses[5]->getCompletedTime());
      $this->assertEquals(10.0, $horses[6]->getCompletedTime());
      $this->assertEquals(10.0, $horses[7]->getCompletedTime());
    }

    /**
     *
     * test the method that calculates the distance the horses covered based on their stats and the time
     */
    public function testCalculateDistanceCovered()
    {
      $horse = (new HorseInRace())
        ->setDistanceCovered(10)
        ->setCompletedTime(10)
        ->setPosition(1)
        ->setHorse((new Horse())
          ->setId(1)
          ->setSpeed(10)
          ->setStrength(9)
          ->setEndurance(9));

      $this->raceSimService->calculateDistanceCoveredAndTime($horse, 10);
      $this->assertEquals(160.0, $horse->getDistanceCovered());


      $horse = (new HorseInRace())
        ->setDistanceCovered(1200)
        ->setCompletedTime(10)
        ->setPosition(1)
        ->setHorse((new Horse())
          ->setId(1)
          ->setSpeed(10)
          ->setStrength(9)
          ->setEndurance(9));

      $this->raceSimService->calculateDistanceCoveredAndTime($horse, 10);
      $this->assertEquals(1307.2000000000003, $horse->getDistanceCovered());
    }
}
