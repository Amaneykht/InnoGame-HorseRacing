<?php

namespace App\Service;

use App\Entity\Horse;
use App\Entity\HorseInRace;
use App\Entity\Race;
use App\Repository\HorseInRaceRepository;
use App\Repository\HorseRepository;
use App\Repository\RaceRepository;
use App\Utils\HorseInRaceSorter;
use App\Utils\RaceValidator;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class RaceSimulationService
{
    /**
     * @var RaceValidator
     */
    private $raceValidator;

    /**
     * @var HorseInRaceSorter
     */
    private $horseSorter;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var HorseInRaceRepository
     */
    private $horseInRaceRepository;

    /**
     * @var HorseRepository
     */
    private $horseRepository;

    /**
     * @var RaceRepository
     */
    private $raceRepository;

    public function __construct(
      EntityManagerInterface $em,
      RaceValidator $raceValidator,
      HorseInRaceRepository $horseInRaceRepository,
      RaceRepository $raceRepository,
      HorseRepository $horseRepository,
      HorseInRaceSorter $horseInRaceSorter
    )
    {
      $this->raceValidator = $raceValidator;
      $this->entityManager = $em;
      $this->raceRepository = $raceRepository;
      $this->horseInRaceRepository = $horseInRaceRepository;
      $this->horseSorter = $horseInRaceSorter;
      $this->horseRepository = $horseRepository;
    }

    /**
     * @param Race $race
     *
     * @return HorseInRace[]
     */
    public function generateRandomHorsesPerRace(Race $race) : array
    {
      $horses = [];
      //get all horses that didn't have races in the same day
      $availableHorses = $this->horseRepository->findAvailableHorses();

      //randomly select 8 from them
      for ($i = 0; $i < $race->getMaxNumberOfHorses(); $i++)
      {
        $horseInRace = new HorseInRace();
        $index = array_rand($availableHorses);
        $horseInRace->setHorse($availableHorses[$index]);
        unset($availableHorses[$index]);
        $horseInRace->setRace($race);
        $horses[] = $horseInRace;
      }

      return $horses;
    }

    /**
     *
     * Create a new race and generate 8 randomly horses for the race
     *
     * @return bool|Race
     */
    public function addNewRaceAndGenerateHorses()
    {
      if ($this->raceValidator->validateNumberOfInProgressRaces()) {

        $race = new Race();
        $race->setStatus(Race::IN_PROGRESS_STATUS);
        $race->setCreatedDateTime(new \DateTime('now'));

        $horses = $this->generateRandomHorsesPerRace($race);

        $this->entityManager->persist($race);
        foreach ($horses as $horse) {
          $this->entityManager->persist($horse);
        }

        $this->entityManager->flush();

        return $race;
      }
      else {
        return false;
      }
    }

    /**
     * Update races progress by number of seconds and update horses progress in the race
     *
     * @param int $numberOfSeconds add progress to the race by this number of seconds
     *
     * @return HorseInRace[]
     */
    public function updateHorseInfoPerRaceByTime(int $numberOfSeconds): array
    {
      $horses = [];

      $races = $this->raceRepository->findInProgressRaces();
      foreach ($races as $race) {
        // get horses in race
        $horses = $race->getHorses() instanceof Collection ? $race->getHorses()->toArray(): $race->getHorses();

        $raceIsDone = true;
        // loop through horses and update their info according to the time
        foreach ($horses as $horse) {
          // calculate distance covered
          $distanceBeforeChangingBytime = $horse->getDistanceCovered();
          $this->calculateDistanceCoveredAndTime($horse, $numberOfSeconds);

          if ($distanceBeforeChangingBytime !== $horse->getDistanceCovered()) {
            $raceIsDone = false;
          }
        }

        if ($raceIsDone) {
          $race->setStatus(Race::COMPLETED_STATUS);
          $this->entityManager->persist($race);
        }

        //Sort By Distance
        $this->horseSorter->setHorses($horses)->sortByDistanceCovered();
        $horses = $this->horseSorter->getHorses();

        // change their position based on the distance
        for ($i = 1; $i <= count($horses); $i++) {
          $horses[$i - 1]->setPosition($i);
          $this->entityManager->persist($horses[$i - 1]);
        }

        $this->entityManager->flush();

        //Sort By Distance
        $this->horseSorter->setHorses($horses)->sortByPosition();
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
   * @return float|null
   */
    public function calculateDistanceCoveredAndTime(HorseInRace $horse, int $numberOfSeconds)
    {
      if ($horse->getDistanceCovered() >= Race::DEFAULT_DISTANCE) {
        return $horse->getDistanceCovered();
      }

      $speed = Horse::BASE_SPEED + $horse->getHorse()->getSpeed();

      // calculate Endurance Distance
      $enduranceDistance = $horse->getHorse()->getEndurance() * 100;

      // calculate Slow Effect by Jockey
      $slowEffect = (HorseInRace::JOCKEY_SLOW_FACTOR - $horse->getHorse()->getStrength() * HorseInRace::JOCKEY_SLOW_PERCENTAGE);

      // calculate distance covered
      $currentNumberOfSecond = 1;
      $distanceCovered = $horse->getDistanceCovered();
      while ($horse->getDistanceCovered() < Race::DEFAULT_DISTANCE && $currentNumberOfSecond <= $numberOfSeconds) {
        if ($distanceCovered >= Race::DEFAULT_DISTANCE) {
          break;
        }

        $distanceCovered = ($distanceCovered >= $enduranceDistance)
          ? $distanceCovered + $speed - $slowEffect
          : $distanceCovered + $speed;

        $horse->setCompletedTime($horse->getCompletedTime() + 1);
        $currentNumberOfSecond++;
      }

      $horse->setDistanceCovered($distanceCovered);
    }
}
