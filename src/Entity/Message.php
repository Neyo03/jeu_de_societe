<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use App\Traits\EntityIdTrait;
use App\Traits\EntityTimestampableTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{

    use EntityTimestampableTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $author;

    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageParticipant::class, orphanRemoval: true)]
    private $messageParticipants;

    #[ORM\ManyToOne(targetEntity: Discussion::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private $discussion;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->messageParticipants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return Collection<int, MessageParticipant>
     */
    public function getMessageParticipants(): Collection
    {
        return $this->messageParticipants;
    }

    public function addMessageParticipant(MessageParticipant $messageParticipant): self
    {
        if (!$this->messageParticipants->contains($messageParticipant)) {
            $this->messageParticipants[] = $messageParticipant;
            $messageParticipant->setMessage($this);
        }

        return $this;
    }

    public function removeMessageParticipant(MessageParticipant $messageParticipant): self
    {
        if ($this->messageParticipants->removeElement($messageParticipant)) {
            // set the owning side to null (unless already changed)
            if ($messageParticipant->getMessage() === $this) {
                $messageParticipant->setMessage(null);
            }
        }

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
}
