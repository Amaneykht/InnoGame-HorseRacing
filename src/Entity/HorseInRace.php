<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HorseInRaceRepository")
 */
class HorseInRace
{
    const JOCKEY_SLOW_PERCENTAGE = 0.08;
    const JOCKEY_SLOW_FACTOR = 5;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $completedTime;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $distanceCovered;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Race", inversedBy="horses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Horse", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $horse;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getDistanceCovered(): ?float
    {
      return $this->distanceCovered;
    }

    public function setDistanceCovered(?float $distanceCovered): self
    {
      $this->distanceCovered = $distanceCovered;

      return $this;
    }

    public function getCompletedTime(): ?\DateTimeInterface
    {
        return $this->completedTime;
    }

    public function setCompletedTime(?\DateTimeInterface $completedTime): self
    {
        $this->completedTime = $completedTime;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getHorse(): ?Horse
    {
        return $this->horse;
    }

    public function setHorse(Horse $horse): self
    {
        $this->horse = $horse;

        return $this;
    }
}
