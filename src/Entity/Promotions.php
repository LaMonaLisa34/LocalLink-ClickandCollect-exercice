<?php

namespace App\Entity;

use App\Repository\PromotionsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: PromotionsRepository::class)]
class Promotions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'promotions')]
    private ?Business $business = null;

    #[ORM\Column(length: 64)]
    private ?string $promotion_name = null;

    #[ORM\Column(length: 255)]
    private ?string $promotion_description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $begin_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column]
    private ?int $reduction = null;

    #[ORM\Column(length: 255)]
    private ?string $imagePromotion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBusiness(): ?Business
    {
        return $this->business;
    }

    public function setBusiness(?Business $business): static
    {
        $this->business = $business;

        return $this;
    }

    public function getPromotionName(): ?string
    {
        return $this->promotion_name;
    }

    public function setPromotionName(string $promotion_name): static
    {
        $this->promotion_name = $promotion_name;

        return $this;
    }

    public function getPromotionDescription(): ?string
    {
        return $this->promotion_description;
    }

    public function setPromotionDescription(string $promotion_description): static
    {
        $this->promotion_description = $promotion_description;

        return $this;
    }

    public function getBeginDate(): ?\DateTimeInterface
    {
        return $this->begin_date;
    }

    public function setBeginDate(\DateTimeInterface $begin_date): static
    {
        $this->begin_date = $begin_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getReduction(): ?int
    {
        return $this->reduction;
    }

    public function setReduction(int $reduction): static
    {
        $this->reduction = $reduction;

        return $this;
    }

    public function getImagePromotion(): ?string
    {
        return $this->imagePromotion;
    }

    public function setImagePromotion(string $imagePromotion): static
    {
        $this->imagePromotion = $imagePromotion;

        return $this;
    }

}
