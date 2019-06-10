<?php declare(strict_types = 1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HorseRepository")
 */
class Horse
{
    const BASE_SPEED = 5;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $speed;

    /**
     * @ORM\Column(type="float")
     */
    private $endurance;

    /**
     * @ORM\Column(type="float")
     */
    private $strength;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function setSpeed(float $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getEndurance(): ?float
    {
      return $this->endurance;
    }

    public function setEndurance(float $endurance): self
    {
      $this->endurance = $endurance;

      return $this;
    }

    public function getStrength(): ?float
    {
      return $this->strength;
    }

    public function setStrength(float $strength): self
    {
      $this->strength = $strength;

      return $this;
    }
}
