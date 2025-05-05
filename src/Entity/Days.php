<?php

namespace App\Entity;

use App\Repository\DaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: DaysRepository::class)]
class Days
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $day_name = null;

    /**
     * @var Collection<int, Planning>
     */
    #[ORM\OneToMany(targetEntity: Planning::class, mappedBy: 'days', orphanRemoval: true)]
    private Collection $plannings;

    public function __construct()
    {
        $this->plannings = new ArrayCollection();
    }

    // #[ORM\OneToMany(targetEntity: Planning::class, mappedBy: 'days')]
    // private Collection $planning;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDayName(): ?string
    {
        return $this->day_name;
    }

    public function setDayName(string $day_name): static
    {
        $this->day_name = $day_name;

        return $this;
    }

    // public function getPlanning(): Collection
    // {
    //     return $this->planning;
    // }

    // public function setPlanning(Planning $planning): static
    // {
    //     // set the owning side of the relation if necessary
    //     if ($planning->getDays() !== $this) {
    //         $planning->setDays($this);
    //     }

    //     $this->planning = $planning;

    //     return $this;
    // }

    /**
     * @return Collection<int, Planning>
     */
    public function getPlannings(): Collection
    {
        return $this->plannings;
    }

    public function addPlanning(Planning $planning): static
    {
        if (!$this->plannings->contains($planning)) {
            $this->plannings->add($planning);
            $planning->setDays($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): static
    {
        if ($this->plannings->removeElement($planning)) {
            // set the owning side to null (unless already changed)
            if ($planning->getDays() === $this) {
                $planning->setDays(null);
            }
        }

        return $this;
    }
    
    
}
