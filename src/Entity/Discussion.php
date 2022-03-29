<?php

namespace App\Entity;

use App\Repository\DiscussionRepository;
use App\Traits\EntityIdTrait;
use App\Traits\EntityTimestampableTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscussionRepository::class)]
class Discussion
{

    use EntityIdTrait;
    use EntityTimestampableTrait;

    #[ORM\Column(type: 'string', length: 255)]
    private $author;

    private $participants = [];

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: DiscussionParticipant::class)]
    private $discussionParticipants;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: Message::class)]
    private $messages;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->messages = new ArrayCollection();
        $this->discussionParticipants = new ArrayCollection();
        
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

    public function getParticipants(): ?array
    {
        return $this->participants;
    }

    public function setParticipants(?array $participants): self
    {
        $this->participants = $participants;

        return $this;
    }

    /**
     * @return Collection<int, DiscussionParticipant>
     */
    public function getDiscussionParticipants(): Collection
    {
        return $this->discussionParticipants;
    }

    public function addDiscussionParticipant(DiscussionParticipant $discussionParticipant): self
    {
        if (!$this->discussionParticipants->contains($discussionParticipant)) {
            $this->discussionParticipants[] = $discussionParticipant;
            $discussionParticipant->setDiscussion($this);
        }

        return $this;
    }

    public function removeDiscussionParticipant(DiscussionParticipant $discussionParticipant): self
    {
        if ($this->discussionParticipants->removeElement($discussionParticipant)) {
            // set the owning side to null (unless already changed)
            if ($discussionParticipant->getDiscussion() === $this) {
                $discussionParticipant->setDiscussion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setDiscussion($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getDiscussion() === $this) {
                $message->setDiscussion(null);
            }
        }

        return $this;
    }
}
