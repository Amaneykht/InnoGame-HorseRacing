<?php

namespace App\Utils;


use App\Entity\HorseInRace;

class HorseInRaceSorter
{
      /**
       * @var HorseInRace[]
       */
      private $horses;

      public function sortByPosition()
      {
        usort($this->horses, function(HorseInRace $a, HorseInRace $b)
        {
          return $a->getPosition() > $b->getPosition();
        });
      }

      public function sortByDistanceCovered()
      {
        usort($this->horses, function(HorseInRace $a, HorseInRace $b)
        {
          return $a->getDistanceCovered() < $b->getDistanceCovered();
        });
      }

      /**
       * @return HorseInRace[]
       */
      public function getHorses(): array
      {
        return $this->horses;
      }

      /**
       * @param HorseInRace[] $horses
       *
       * @return $this
       */
      public function setHorses(array $horses): self
      {
        $this->horses = $horses;

        return $this;
      }
}