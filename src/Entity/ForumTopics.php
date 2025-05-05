<?php

namespace App\Entity;

use App\Repository\ForumTopicsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: ForumTopicsRepository::class)]
class ForumTopics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'forumTopics')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $author_forumSubject = null;

    #[ORM\Column(length: 64)]
    private ?string $title_topic = null;

    #[ORM\Column(length: 255)]
    private ?string $message_topic = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateTime_topic = null;

    /**
     * @var Collection<int, ForumReplies>
     */
    #[ORM\OneToMany(targetEntity: ForumReplies::class, mappedBy: 'topic', orphanRemoval: true)]
    private Collection $forumReplies;

    public function __construct()
    {
        $this->forumReplies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorForumSubject(): ?user
    {
        return $this->author_forumSubject;
    }

    public function setAuthorForumSubject(?user $author_forumSubject): static
    {
        $this->author_forumSubject = $author_forumSubject;

        return $this;
    }

    public function getTitleTopic(): ?string
    {
        return $this->title_topic;
    }

    public function setTitleTopic(string $title_topic): static
    {
        $this->title_topic = $title_topic;

        return $this;
    }

    public function getMessageTopic(): ?string
    {
        return $this->message_topic;
    }

    public function setMessageTopic(string $message_topic): static
    {
        $this->message_topic = $message_topic;

        return $this;
    }

    public function getDateTimeTopic(): ?\DateTimeInterface
    {
        return $this->DateTime_topic;
    }

    public function setDateTimeTopic(\DateTimeInterface $DateTime_topic): static
    {
        $this->DateTime_topic = $DateTime_topic;

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
            $forumReply->setTopic($this);
        }

        return $this;
    }

    public function removeForumReply(ForumReplies $forumReply): static
    {
        if ($this->forumReplies->removeElement($forumReply)) {
            // set the owning side to null (unless already changed)
            if ($forumReply->getTopic() === $this) {
                $forumReply->setTopic(null);
            }
        }

        return $this;
    }
}
