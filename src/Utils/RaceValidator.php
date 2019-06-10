<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Utils;

use App\Entity\Race;
use App\Repository\RaceRepository;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
 * This class is used for all validations related to Race creation or update
 */
class RaceValidator
{
    const MAXIMUM_NUMBER_OF_RACES = 3;

    /**
     * @var RaceRepository
     */
      private $raceRepository;

      public function __construct(RaceRepository $races)
      {
        $this->raceRepository = $races;
      }

    /**
     * @return bool true if the races that are currently running less than allowed maximum number of running races, false otherwise
     *
     * @throws
     */
      public function validateNumberOfInProgressRaces(): bool
      {
        $races = $this->raceRepository->findInProgressRaces();
        if ($races && count($races) >= self::MAXIMUM_NUMBER_OF_RACES) {
          throw new InvalidArgumentException('Sorry you reach the maximum number of running races');
        }

        return true;
      }
}
