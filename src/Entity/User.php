<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Traits\EntityIdTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Rfc4122\UuidInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use EntityIdTrait;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * 
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\Length(max="180")
     */
    private $email;

    /**
     * @SerializedName("password")
     * 
     * @Assert\NotBlank()
     * @Assert\Length(
     *  min="6",
     *  max="32",
     *  minMessage="Your password must be at lead {{ limit }} character long ",
     *  maxMessage="Your password cannot be longer than {{ limit }} characters "
     * )
     * @Assert\Regex(
     *  "/^.*(?=.{8,})((?=.*[!@#$%^&*()\-_=+{};:,<.>]){1})(?=.*\d)((?=.*[a-z]){1})((?=.*[A-Z]){1}).*$/",
     *  message="Your password needs an uppercase, a lowercase, a digit and a special character"
     * )
     */
    private $plainPassword;

    #[ORM\Column(type: 'boolean')]
    private $isEnabled;

    #[ORM\Column(type: 'string', length: 255)]
    private $validationToken;

    #[ORM\Column(type: 'datetime')]
    private $tokenValidationExpireAt;

    
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uuid;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getValidationToken(): ?string
    {
        return $this->validationToken;
    }

    public function setValidationToken(string $validationToken): self
    {
        $this->validationToken = $validationToken;

        return $this;
    }

    public function getTokenValidationExpireAt(): ?\DateTime
    {
        return $this->tokenValidationExpireAt;
    }

    public function setTokenValidationExpireAt(\DateTime $tokenValidationExpireAt): self
    {
        $this->tokenValidationExpireAt = $tokenValidationExpireAt;

        return $this;
    }

    public function generateValidationToken(){

        $expiration  = new DateTime('+1 day');
        $token = rtrim(strtr(base64_encode(random_bytes(32)),'+/','-_'), '=');

        $this->setValidationToken($token);
        $this->setTokenValidationExpireAt($expiration);
    }
}
