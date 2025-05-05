<?php

namespace App\Entity;

use App\Repository\EventsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: EventsRepository::class)]
class Events
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?business $business = null;

    #[ORM\Column(length: 128)]
    private ?string $NameEvent = null;

    #[ORM\Column(length: 255)]
    private ?string $DescriptionEvent = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $BeginDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $EndDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBusiness(): ?business
    {
        return $this->business;
    }

    public function setBusiness(?business $business): static
    {
        $this->business = $business;

        return $this;
    }

    public function getNameEvent(): ?string
    {
        return $this->NameEvent;
    }

    public function setNameEvent(string $NameEvent): static
    {
        $this->NameEvent = $NameEvent;

        return $this;
    }

    public function getDescriptionEvent(): ?string
    {
        return $this->DescriptionEvent;
    }

    public function setDescriptionEvent(string $DescriptionEvent): static
    {
        $this->DescriptionEvent = $DescriptionEvent;

        return $this;
    }

    public function getBeginDate(): ?\DateTimeInterface
    {
        return $this->BeginDate;
    }

    public function setBeginDate(\DateTimeInterface $BeginDate): static
    {
        $this->BeginDate = $BeginDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->EndDate;
    }

    public function setEndDate(\DateTimeInterface $EndDate): static
    {
        $this->EndDate = $EndDate;

        return $this;
    }
}
