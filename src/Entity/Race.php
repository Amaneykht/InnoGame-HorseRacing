<?php declare(strict_types = 1);

namespace App\Entity;

use App\Repository\HorseInRaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RaceRepository")
 */
class Race
{
    const DEFAULT_DISTANCE = 1500;
    const MAXIMUM_NUMBER_OF_HORSES = 8;

    const IN_PROGRESS_STATUS = 1;
    const COMPLETED_STATUS = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDateTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maximumDistance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxNumberOfHorses;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\HorseInRace", mappedBy="race")
     */
    private $horses;

    public function __construct()
    {
        $this->horses = new ArrayCollection();
        $this->maximumDistance = self::DEFAULT_DISTANCE;
        $this->maxNumberOfHorses = self::MAXIMUM_NUMBER_OF_HORSES;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
      $this->id = $id;

      return $this;
    }

    public function getCreatedDateTime(): ?\DateTimeInterface
    {
        return $this->createdDateTime;
    }

    public function setCreatedDateTime(\DateTimeInterface $createdDateTime): self
    {
        $this->createdDateTime = $createdDateTime;

        return $this;
    }

    public function getMaximumDistance(): ?int
    {
        return $this->maximumDistance;
    }

    public function setMaximumDistance(?int $maximumDistance): self
    {
        $this->maximumDistance = $maximumDistance;

        return $this;
    }

    public function getMaxNumberOfHorses(): ?int
    {
        return $this->maxNumberOfHorses;
    }

    public function setMaxNumberOfHorses(?int $maxNumberOfHorses): self
    {
        $this->maxNumberOfHorses = $maxNumberOfHorses;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection|HorseInRace[]
     */
    public function getHorses()
    {
        return $this->horses;
    }

    public function setHorses(array $horses): self
    {
      $this->horses = $horses;

      return $this;
    }
}
