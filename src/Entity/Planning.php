<?php

namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: PlanningRepository::class)]
class Planning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $opening_hour = null;

    #[ORM\Column(length: 32)]
    private ?string $closing_hour = null;

    #[ORM\ManyToOne(inversedBy: 'plannings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Days $days = null;

    /**
     * @var Collection<int, Business>
     */
    #[ORM\ManyToMany(targetEntity: Business::class, inversedBy: 'plannings')]
    private Collection $business;

    public function __construct()
    {
        $this->business = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOpeningHour(): ?string
    {
        return $this->opening_hour;
    }

    public function setOpeningHour(string $opening_hour): static
    {
        $this->opening_hour = $opening_hour;

        return $this;
    }

    public function getClosingHour(): ?string
    {
        return $this->closing_hour;
    }

    public function setClosingHour(string $closing_hour): static
    {
        $this->closing_hour = $closing_hour;

        return $this;
    }

    public function getDays(): ?Days
    {
        return $this->days;
    }

    public function setDays(?Days $days): static
    {
        $this->days = $days;

        return $this;
    }

    /**
     * @return Collection<int, Business>
     */
    public function getBusiness(): Collection
    {
        return $this->business;
    }

    public function addBusiness(Business $business): static
    {
        if (!$this->business->contains($business)) {
            $this->business->add($business);
        }

        return $this;
    }

    public function removeBusiness(Business $business): static
    {
        $this->business->removeElement($business);

        return $this;
    }
}
