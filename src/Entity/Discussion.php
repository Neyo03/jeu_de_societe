<?php

namespace App\Entity;

use App\Repository\DiscussionRepository;
use App\Traits\EntityAuthorTrait;
use App\Traits\EntityIdTrait;
use App\Traits\EntityTimestampableTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: DiscussionRepository::class)]
class Discussion
{

    use EntityIdTrait;
    use EntityTimestampableTrait;
    use EntityAuthorTrait;
   

    private $participants = [];

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: DiscussionParticipant::class, cascade:["persist"])]
    private $discussionParticipants;

    #[ORM\OneToMany(mappedBy: 'discussion', targetEntity: Message::class, cascade:["persist"])]
    private $messages;

    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
        $this->createdAt = new DateTime();
        $this->updatedAt= new DateTime();
        $this->messages = new ArrayCollection();
        $this->discussionParticipants = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int,DiscussionParticipant>
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
     * @return Collection<int,Message>
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


    public function addParticipant(User $user)
    {
        $discussionParticipant = new DiscussionParticipant();
        $discussionParticipant->setDiscussion($this);
        $discussionParticipant->setParticipant($user);

        $this->addDiscussionParticipant($discussionParticipant);
    }

    public function getAllParticipants()
    {
        $participants = [];
        foreach ($this->getDiscussionParticipants()->getValues() as $participant ) {
            $participants [] = $participant->getParticipant();
        }
        
        return $participants;
    }

    public function isParticipantInDiscussion(User $participant){
        $allParticipants = $this->getAllParticipants();
        return in_array($participant, $allParticipants);
    }
}
