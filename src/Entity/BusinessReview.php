<?php

namespace App\Entity;

use App\Repository\BusinessReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: BusinessReviewRepository::class)]
class BusinessReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'businessReviews')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'businessReviews')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Business $business = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column]
    private ?int $business_score = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $response = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
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

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function getBusinessScore(): ?int
    {
        return $this->business_score;
    }

    public function setBusinessScore(int $business_score): static
    {
        $this->business_score = $business_score;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): static
    {
        $this->response = $response;

        return $this;
    }
}
