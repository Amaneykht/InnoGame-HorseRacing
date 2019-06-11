<?php

namespace App\Controller;

use App\Entity\Race;
use App\Repository\HorseInRaceRepository;
use App\Repository\RaceRepository;

use App\Service\RaceSimulationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

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
     */
    public function index(Request $request, RaceRepository $races, HorseInRaceRepository $horse): Response
    {
        $currentRaces = $races->findInProgressRaces();
        $lastfiveRaces = $races->findLastFiveRaces();
        $bestTimeAndHorseStats = $horse->findBestTimeWithHorseStats();

        return $this->render('race-simulation/index.html.twig', [
            'currentRaces' => $currentRaces,
            'lastfiveRaces' => $lastfiveRaces,
            'bestTimeAndHorseStats' => $bestTimeAndHorseStats[0]
        ]);
    }

    /**
     * @Route("/add", methods={"GET"}, name="race_simulation_add")
     */
    public function raceAdd(Request $request, HorseInRaceRepository $horse, RaceSimulationService $raceSimulationService): Response
    {
        $horsesInRace = [];
        $errorMessage = "";

        if ($request->isXmlHttpRequest()) {
          try {
            $race = $raceSimulationService->addNewRaceAndGenerateHorses();

            if ($race !== false)
            {
              $horsesInRace = $horse->getHorsesInfoByRace($race);
            }
          }
          catch(\Exception $e) {
            $errorMessage = $e->getMessage();
          }
        }

        return $this->json([
          'data' => $horsesInRace,
          'errorMessage'=> $errorMessage
        ]);
    }

    /**
     * @Route("/progress", methods={"GET"}, name="race_simulation_progress")
     */
    public function raceProgress(Request $request, HorseInRaceRepository $horse, RaceSimulationService $raceSimulationService, SerializerInterface $serializer): Response
    {
      $horsesInRace = [];

      if ($request->isXmlHttpRequest())
      {
        $horsesInRace = $raceSimulationService->updateHorseInfoPerRaceByTime( self::NUMBER_OF_SECONDS);
      }

      $jsonObject = $serializer->serialize($horsesInRace, 'json', [
        'circular_reference_handler' => function ($object) {
          return $object->getId();
        }
      ]);

      return new Response($jsonObject, 200, ['Content-Type' => 'application/json']);
    }
}
