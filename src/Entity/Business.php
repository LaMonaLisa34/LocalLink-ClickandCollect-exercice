<?php

namespace App\Entity;

use App\Repository\BusinessRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: BusinessRepository::class)]
class Business
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column(length: 30)]
    private ?string $phone = null;

    #[ORM\Column(length: 1064)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'business')]
    // #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'business')]
    private Collection $products;

    #[ORM\Column]
    private ?int $statistic = null;

    #[ORM\Column]
    private ?float $footprintCarbon = null;

    /**
     * @var Collection<int, Label>
     */
    #[ORM\ManyToMany(targetEntity: Label::class, inversedBy: 'businesses')]
    private Collection $label;

    #[ORM\ManyToOne(inversedBy: 'businesses')]
    #[ORM\JoinColumn(nullable: false)]
    // private ?Week $planning = null;

    /**
     * @var Collection<int, BusinessPhotos>
     */
    #[ORM\OneToMany(targetEntity: BusinessPhotos::class, mappedBy: 'business')]
    private Collection $businessPhotos;

    #[ORM\ManyToOne(inversedBy: 'business')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $categories = null;

    /**
     * @var Collection<int, Promotions>
     */
    #[ORM\OneToMany(targetEntity: Promotions::class, mappedBy: 'business')]
    private Collection $promotions;

    /**
     * @var Collection<int, BusinessReview>
     */
    #[ORM\OneToMany(targetEntity: BusinessReview::class, mappedBy: 'business')]
    private Collection $businessReviews;

    // /**
    //  * @var Collection<int, Planning>
    //  */
    // #[ORM\ManyToMany(targetEntity: Planning::class, mappedBy: 'business')]
    // private Collection $plannings;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $streetNumber = null;

    #[ORM\Column(length: 64)]
    private ?string $streetName = null;

    #[ORM\Column(length: 64)]
    private ?string $CityName = null;

    #[ORM\Column]
    private ?bool $validated = null;
    

    /**
     * @var Collection<int, Events>
     */
    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'business', orphanRemoval: true)]
    private Collection $events;

    /**
     * @var Collection<int, Planning>
     */
    #[ORM\ManyToMany(targetEntity: Planning::class, mappedBy: 'business')]
    private Collection $plannings;



    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->label = new ArrayCollection();
        $this->businessPhotos = new ArrayCollection();
        $this->promotions = new ArrayCollection();
        $this->businessReviews = new ArrayCollection();
        $this->events = new ArrayCollection();
        $this->plannings = new ArrayCollection();
        $this->carts = new ArrayCollection();
        $this->commands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setBusiness($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBusiness() === $this) {
                $product->setBusiness(null);
            }
        }

        return $this;
    }

    public function getStatistic(): ?int
    {
        return $this->statistic;
    }

    public function setStatistic(int $statistic): static
    {
        $this->statistic = $statistic;

        return $this;
    }

    public function getFootprintCarbon(): ?float
    {
        return $this->footprintCarbon;
    }

    public function setFootprintCarbon(float $footprintCarbon): static
    {
        $this->footprintCarbon = $footprintCarbon;

        return $this;
    }

    /**
     * @return Collection<int, Label>
     */
    public function getLabel(): Collection
    {
        return $this->label;
    }

    public function addLabel(Label $label): static
    {
        if (!$this->label->contains($label)) {
            $this->label->add($label);
        }

        return $this;
    }

    public function removeLabel(Label $label): static
    {
        $this->label->removeElement($label);

        return $this;
    }



    /**
     * @return Collection<int, BusinessPhotos>
     */
    public function getBusinessPhotos(): Collection
    {
        return $this->businessPhotos;
    }

    public function addBusinessPhoto(BusinessPhotos $businessPhoto): static
    {
        if (!$this->businessPhotos->contains($businessPhoto)) {
            $this->businessPhotos->add($businessPhoto);
            $businessPhoto->setBusiness($this);
        }

        return $this;
    }

    public function removeBusinessPhoto(BusinessPhotos $businessPhoto): static
    {
        if ($this->businessPhotos->removeElement($businessPhoto)) {
            // set the owning side to null (unless already changed)
            if ($businessPhoto->getBusiness() === $this) {
                $businessPhoto->setBusiness(null);
            }
        }

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, Promotions>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotions $promotion): static
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions->add($promotion);
            $promotion->setBusiness($this);
        }

        return $this;
    }

    public function removePromotion(Promotions $promotion): static
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getBusiness() === $this) {
                $promotion->setBusiness(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BusinessReview>
     */
    public function getBusinessReviews(): Collection
    {
        return $this->businessReviews;
    }

    public function addBusinessReview(BusinessReview $businessReview): static
    {
        if (!$this->businessReviews->contains($businessReview)) {
            $this->businessReviews->add($businessReview);
            $businessReview->setBusiness($this);
        }

        return $this;
    }

    public function removeBusinessReview(BusinessReview $businessReview): static
    {
        if ($this->businessReviews->removeElement($businessReview)) {
            // set the owning side to null (unless already changed)
            if ($businessReview->getBusiness() === $this) {
                $businessReview->setBusiness(null);
            }
        }

        return $this;
    }

    // /**
    //  * @return Collection<int, Planning>
    //  */
    // public function getPlannings(): Collection
    // {
    //     return $this->plannings;
    // }

    // public function addPlanning(Planning $planning): static
    // {
    //     if (!$this->plannings->contains($planning)) {
    //         $this->plannings->add($planning);
    //         $planning->addBusiness($this);
    //     }

    //     return $this;
    // }

    // public function removePlanning(Planning $planning): static
    // {
    //     if ($this->plannings->removeElement($planning)) {
    //         $planning->removeBusiness($this);
    //     }

    //     return $this;
    // }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?string $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(string $streetName): static
    {
        $this->streetName = $streetName;

        return $this;
    }

    public function getCityName(): ?string
    {
        return $this->CityName;
    }

    public function setCityName(string $CityName): static
    {
        $this->CityName = $CityName;

        return $this;
    }

    public function isValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): static
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * @return Collection<int, Events>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Events $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setBusiness($this);
        }

        return $this;
    }

    public function removeEvent(Events $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getBusiness() === $this) {
                $event->setBusiness(null);
            }
        }

        return $this;
    }

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
            $planning->addBusiness($this);
        }

        return $this;
    }

    public function removePlanning(Planning $planning): static
    {
        if ($this->plannings->removeElement($planning)) {
            $planning->removeBusiness($this);
        }

        return $this;
    }


    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $rejectionReason = null;

    #[ORM\Column]
    private ?float $lat = null;

    #[ORM\Column]
    private ?float $lon = null;

    /**
     * @var Collection<int, Cart>
     */
    #[ORM\OneToMany(targetEntity: Cart::class, mappedBy: 'business', orphanRemoval: true)]
    private Collection $carts;

    /**
     * @var Collection<int, Command>
     */
    #[ORM\OneToMany(targetEntity: Command::class, mappedBy: 'business', orphanRemoval: true)]
    private Collection $commands;

    

    public function getRejectionReason(): ?string
    {
        return $this->rejectionReason;
    }

    public function setRejectionReason(?string $rejectionReason): self
    {
        $this->rejectionReason = $rejectionReason;
        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): static
    {
        $this->lon = $lon;

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): static
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->setBusiness($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getBusiness() === $this) {
                $cart->setBusiness(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): static
    {
        if (!$this->commands->contains($command)) {
            $this->commands->add($command);
            $command->setBusiness($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): static
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getBusiness() === $this) {
                $command->setBusiness(null);
            }
        }

        return $this;
    }

    
    
}
