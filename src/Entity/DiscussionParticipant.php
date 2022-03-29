<?php

namespace App\Entity;

use App\Repository\DiscussionParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionParticipantRepository::class)]
class DiscussionParticipant
{

    const NOT_READ = 0;
    const READ = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $status;

    #[ORM\ManyToOne(targetEntity: Discussion::class, inversedBy: 'discussionParticipants')]
    #[ORM\JoinColumn(nullable: false)]
    private $discussion;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'discussionParticipants')]
    #[ORM\JoinColumn(nullable: false)]
    private $participant;

    public function __construct()
    {
       $this->status = self::NOT_READ;
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): self
    {
        $this->discussion = $discussion;

        return $this;
    }

    public function getParticipant(): ?User
    {
        return $this->participant;
    }

    public function setParticipant(?User $participant): self
    {
        $this->participant = $participant;

        return $this;
    }
}
