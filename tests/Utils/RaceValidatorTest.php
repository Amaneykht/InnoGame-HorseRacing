<?php

namespace App\Tests\Utils;

use App\Entity\Race;
use App\Repository\RaceRepository;
use App\Utils\RaceValidator;
use App\Utils\Validator;
use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    private $raceRepository;
    private $objectManager;
    private $raceValidator;

    public function __construct()
    {
      // mock the repositories
      $this->raceRepository = $this->createMock(RaceRepository::class);
      $this->raceValidator = $this->createMock(RaceValidator::class);

      // mock the EntityManager to return the mock of the repository
      $this->objectManager = $this->createMock(ObjectManager::class);
    }

    public function testValidateNumberOfInProgressRacesThreeOrMore()
    {
        $this->raceRepository->expects($this->any())
          ->method('findInProgressRaces')
          ->willReturn([
            (new Race())
              ->setId(1)
              ->setStatus(1)
              ->setCreatedDateTime(new \DateTime('2019-06-10 19:00:00'))
            ,
            (new Race())
              ->setId(2)
              ->setStatus(1)
              ->setCreatedDateTime(new \DateTime('2019-06-10 20:00:00'))
            ,
            (new Race())
              ->setId(3)
              ->setStatus(1)
              ->setCreatedDateTime(new \DateTime('2019-06-10 21:00:00'))
            ,
          ]);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Sorry you reach the maximum number of running races');
        $this->raceValidator->validateNumberOfInProgressRaces();
    }

  public function testValidateNumberOfInProgressRacesLessThanThree()
  {
    $this->raceRepository->expects($this->any())
      ->method('findInProgressRaces')
      ->willReturn([
        (new Race())
          ->setId(1)
          ->setStatus(1)
          ->setCreatedDateTime(new \DateTime('2019-06-10 19:00:00'))
        ,
        (new Race())
          ->setId(2)
          ->setStatus(1)
          ->setCreatedDateTime(new \DateTime('2019-06-10 20:00:00'))
        ,
      ]);

    $this->assertSame(true, $this->raceValidator->validateNumberOfInProgressRaces());
  }
}
