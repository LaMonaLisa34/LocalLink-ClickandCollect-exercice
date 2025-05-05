<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 128)]
    private ?string $category = null;

    /**
     * @var Collection<int, Business>
     */
    #[ORM\OneToMany(targetEntity: Business::class, mappedBy: 'categories')]
    private Collection $business;

    public function __construct()
    {
        $this->business = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

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
            $business->setCategories($this);
        }

        return $this;
    }

    public function removeBusiness(Business $business): static
    {
        if ($this->business->removeElement($business)) {
            
            if ($business->getCategories() === $this) {
                $business->setCategories(null);
            }
        }

        return $this;
    }
    
}
