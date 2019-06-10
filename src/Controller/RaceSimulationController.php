<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Race;
use App\Repository\HorseInRaceRepository;
use App\Repository\RaceRepository;

use App\Service\RaceSimulationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller used to manage current races and get information about existing races
 *
 * @Route("/race-simulation")
 *
 */
class RaceSimulationController extends AbstractController
{
      const NUMBER_OF_SECONDS = 10;

      /**
       * @Route("/", methods={"GET"}, name="race_simulation_index")
       *
       */
      public function index(Request $request, RaceRepository $races): Response
      {
          $currentRaces = $races->findInProgressRaces();

          return $this->render('race-simulation/index.html.twig', [
              'currentRaces' => $currentRaces,
          ]);
      }

    /**
     * @Route("/completed", methods={"GET"}, name="race_simulation_completed")
     * @param RaceRepository $races
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\ORMException
     */
      public function completedRacesInfo(RaceRepository $races, HorseInRaceRepository $horse): Response
      {
          $last5Races = $races->findLastFiveRaces();
          $bestTimeAndHorseStats = $horse->findBestTimeWithHorseStats();

          return $this->render('race-simulation/races-info.html.twig', [
            'last5Races' => $last5Races,
            'bestTimeAndHorseStats' => $bestTimeAndHorseStats
          ]);
      }

      /**
       * @Route("/add", methods={"GET"}, name="race_simulation_add")
       */
      public function raceAdd(Request $request, HorseInRaceRepository $horse, RaceSimulationService $raceSimulationService): Response
      {
          $horsesInRace = [];

          if (!$request->isXmlHttpRequest()) {
            $race = $raceSimulationService->addNewRaceAndGenerateHorses();
            $horsesInRace = $horse->getHorsesInfoByRace($race);
          }

          return $this->json($horsesInRace);
      }

    /**
     * @Route("/progress", methods={"GET"}, name="race_simulation_progress")
     */
    public function raceProgress(Request $request, HorseInRaceRepository $horse, RaceSimulationService $raceSimulationService): Response
    {
      $horsesInRace = [];

      if (!$request->isXmlHttpRequest()) {
        $raceId = $request->query->get('id', '');
        $numberOfSeconds = self::NUMBER_OF_SECONDS;

        $race = $raceSimulationService->updateHorseInfoPerRaceByTime($raceId, $numberOfSeconds);
        $horsesInRace = $horse->getHorsesInfoByRace($race);
      }

      return $this->json($horsesInRace);
    }
}
