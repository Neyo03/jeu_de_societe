<?php

namespace App\Entity;

use App\Repository\MessageParticipantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageParticipantRepository::class)]
class MessageParticipant implements \JsonSerializable
{

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return [
            "id" => $this->id,
            "status" => $this->status,
            'participant' => $this->participant,
            'messageid'=> $this->getMessage()->getId()
            
        ];
    }

    const NOT_READ = 0;
    const READ = 1;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $status;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'messageParticipants', cascade:["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private $message;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messageParticipants', cascade:["persist"])]
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

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): self
    {
        $this->message = $message;

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
