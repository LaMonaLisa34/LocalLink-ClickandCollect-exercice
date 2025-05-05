<?php

namespace App\Entity;

use App\Repository\ForumRepliesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource]
#[ORM\Entity(repositoryClass: ForumRepliesRepository::class)]
class ForumReplies
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'forumReplies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ForumTopics $topic = null;

    #[ORM\ManyToOne(inversedBy: 'forumReplies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $author_forumReply = null;

    #[ORM\Column(length: 255)]
    private ?string $message_reply = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateTime_Reply = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTopic(): ?forumtopics
    {
        return $this->topic;
    }

    public function setTopic(?forumtopics $topic): static
    {
        $this->topic = $topic;

        return $this;
    }

    public function getAuthorForumReply(): ?user
    {
        return $this->author_forumReply;
    }

    public function setAuthorForumReply(?user $author_forumReply): static
    {
        $this->author_forumReply = $author_forumReply;

        return $this;
    }

    public function getMessageReply(): ?string
    {
        return $this->message_reply;
    }

    public function setMessageReply(string $message_reply): static
    {
        $this->message_reply = $message_reply;

        return $this;
    }

    public function getDateTimeReply(): ?\DateTimeInterface
    {
        return $this->dateTime_Reply;
    }

    public function setDateTimeReply(\DateTimeInterface $dateTime_Reply): static
    {
        $this->dateTime_Reply = $dateTime_Reply;

        return $this;
    }
}
