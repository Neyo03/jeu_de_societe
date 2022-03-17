<?php

namespace App\Entity;

use App\Repository\ValidationUserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ValidationUserRepository::class)]
class ValidationUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'validationUsers')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $tokenValidation;

    #[ORM\Column(type: 'datetime')]
    private $tokenValidationExpiredAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTokenValidation(): ?string
    {
        return $this->tokenValidation;
    }

    public function setTokenValidation(string $tokenValidation): self
    {
        $this->tokenValidation = $tokenValidation;

        return $this;
    }

    public function getTokenValidationExpiredAt(): ?\DateTimeInterface
    {
        return $this->tokenValidationExpiredAt;
    }

    public function setTokenValidationExpiredAt(\DateTimeInterface $tokenValidationExpiredAt): self
    {
        $this->tokenValidationExpiredAt = $tokenValidationExpiredAt;

        return $this;
    }

    public function generateValidationToken($user){

        $expiration  = new DateTime('+1 day');
        $token = rtrim(strtr(base64_encode(random_bytes(32)),'+/','-_'), '=');
        $this->setUser($user);
        $this->setTokenValidation($token);
        $this->setTokenValidationExpiredAt($expiration);
    }


}
