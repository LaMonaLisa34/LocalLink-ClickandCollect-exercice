<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30)]
    private ?string $phone = null;

    #[ORM\OneToMany(targetEntity: Business::class, mappedBy: 'owner')]
    private Collection $business;

    /**
     * @var Collection<int, ProductReview>
     */
    #[ORM\OneToMany(targetEntity: ProductReview::class, mappedBy: 'user')]
    private Collection $productReviews;

    /**
     * @var Collection<int, BusinessReview>
     */
    #[ORM\OneToMany(targetEntity: BusinessReview::class, mappedBy: 'user')]
    private Collection $businessReviews;

    #[ORM\Column(length: 32)]
    private ?string $firstName = null;

    #[ORM\Column(length: 32)]
    private ?string $lastName = null;

    /**
     * @var Collection<int, ForumTopics>
     */
    #[ORM\OneToMany(targetEntity: ForumTopics::class, mappedBy: 'author_forumSubject')]
    private Collection $forumTopics;

    /**
     * @var Collection<int, ForumReplies>
     */
    #[ORM\OneToMany(targetEntity: ForumReplies::class, mappedBy: 'author_forumReply', orphanRemoval: true)]
    private Collection $forumReplies;

    /**
     * @var Collection<int, Cart>
     */
    #[ORM\OneToMany(targetEntity: Cart::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $carts;

    public function __construct()
    {
        $this->carts = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getBusiness(): Collection
    {
        return $this->business;
    }

    public function setBusiness(Business $business): static
    {
        // set the owning side of the relation if necessary
        if ($business->getOwner() !== $this) {
            $business->setOwner($this);
        }

        $this->business = $business;

        return $this;
    }


    /**
     * @return Collection<int, ProductReview>
     */
    public function getProductReviews(): Collection
    {
        return $this->productReviews;
    }

    public function addProductReview(ProductReview $productReview): static
    {
        if (!$this->productReviews->contains($productReview)) {
            $this->productReviews->add($productReview);
            $productReview->setUser($this);
        }

        return $this;
    }

    public function removeProductReview(ProductReview $productReview): static
    {
        if ($this->productReviews->removeElement($productReview)) {
            // set the owning side to null (unless already changed)
            if ($productReview->getUser() === $this) {
                $productReview->setUser(null);
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
            $businessReview->setUser($this);
        }

        return $this;
    }

    public function removeBusinessReview(BusinessReview $businessReview): static
    {
        if ($this->businessReviews->removeElement($businessReview)) {
            // set the owning side to null (unless already changed)
            if ($businessReview->getUser() === $this) {
                $businessReview->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, ForumTopics>
     */
    public function getForumTopics(): Collection
    {
        return $this->forumTopics;
    }

    public function addForumTopic(ForumTopics $forumTopic): static
    {
        if (!$this->forumTopics->contains($forumTopic)) {
            $this->forumTopics->add($forumTopic);
            $forumTopic->setAuthorForumSubject($this);
        }

        return $this;
    }

    public function removeForumTopic(ForumTopics $forumTopic): static
    {
        if ($this->forumTopics->removeElement($forumTopic)) {
            // set the owning side to null (unless already changed)
            if ($forumTopic->getAuthorForumSubject() === $this) {
                $forumTopic->setAuthorForumSubject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ForumReplies>
     */
    public function getForumReplies(): Collection
    {
        return $this->forumReplies;
    }

    public function addForumReply(ForumReplies $forumReply): static
    {
        if (!$this->forumReplies->contains($forumReply)) {
            $this->forumReplies->add($forumReply);
            $forumReply->setAuthorForumReply($this);
        }

        return $this;
    }

    public function removeForumReply(ForumReplies $forumReply): static
    {
        if ($this->forumReplies->removeElement($forumReply)) {
            // set the owning side to null (unless already changed)
            if ($forumReply->getAuthorForumReply() === $this) {
                $forumReply->setAuthorForumReply(null);
            }
        }

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
            $cart->setOwner($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getOwner() === $this) {
                $cart->setOwner(null);
            }
        }

        return $this;
    }

    

}
