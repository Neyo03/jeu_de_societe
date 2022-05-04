<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Traits\EntityIdTrait;
use App\Traits\EntityTimestampableTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Rfc4122\UuidInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['uuid'], message: 'There is already an account with this uuid')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \JsonSerializable
{
    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return [
            "id" => $this->id,
            "pseudo" => $this->pseudo,
            'email' => $this->email
            
        ];
    }

    use EntityIdTrait;
    use EntityTimestampableTrait;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;


    #[ORM\Column(type: 'string',length:180, unique:true)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[Assert\Length(max:"180")]
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

    #[Assert\EqualTo(propertyPath:"plainPassword", message:"This value should be equal to password")]
    private $secondePlainPassword;

    #[ORM\Column(type: 'boolean')]
    private $isEnabled;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ValidationUser::class, cascade:["persist"])]
    private $validationUsers;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'string', length: 255)]
    private $pseudo;

    #[ORM\Column(type: 'string', length: 255, nullable:true)]
    private $profilPicture;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $lastLoginAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $color;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: MessageParticipant::class, cascade:["persist"])]
    private $messageParticipants;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: DiscussionParticipant::class, cascade:["persist"])]
    private $discussionParticipants;


    public function __construct()
    {
        $this->uuid = Uuid::uuid4();
        $this->createdAt = new DateTime();
        $this->updatedAt= new DateTime();
        $this->profilPicture = "sans-visage.webp";
        $this->lastName = "";
        $this->firstName ="";
        $this->pseudo ="";
        $this->isEnabled = false;
        $this->validationUsers = new ArrayCollection();
        $this->messageParticipants = new ArrayCollection();
        $this->discussionParticipants = new ArrayCollection();
    }

    
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
        $this->secondPlainPassword = null;
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

   
    /**
     * @return Collection<int, ValidationUser>
     */
    public function getValidationUsers(): Collection
    {
        return $this->validationUsers;
    }

    public function addValidationUser(ValidationUser $validationUser): self
    {
        if (!$this->validationUsers->contains($validationUser)) {
            $this->validationUsers[] = $validationUser;
            $validationUser->setUser($this);
        }

        return $this;
    }

    public function removeValidationUser(ValidationUser $validationUser): self
    {
        if ($this->validationUsers->removeElement($validationUser)) {
            // set the owning side to null (unless already changed)
            if ($validationUser->getUser() === $this) {
                $validationUser->setUser(null);
            }
        }

        return $this;
    }

    public function getSecondePlainPassword(): ?string
    {
        return $this->secondePlainPassword;
    }

    public function setSecondePlainPassword(string $secondePlainPassword): self
    {
        $this->secondePlainPassword = $secondePlainPassword;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getProfilPicture(): ?string
    {
        return $this->profilPicture;
    }

    public function setProfilPicture(string $profilPicture): self
    {
        $this->profilPicture = $profilPicture;

        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

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
            $messageParticipant->setParticipant($this);
        }

        return $this;
    }

    public function removeMessageParticipant(MessageParticipant $messageParticipant): self
    {
        if ($this->messageParticipants->removeElement($messageParticipant)) {
            // set the owning side to null (unless already changed)
            if ($messageParticipant->getParticipant() === $this) {
                $messageParticipant->setParticipant(null);
            }
        }

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
            $discussionParticipant->setParticipant($this);
        }

        return $this;
    }

    public function removeDiscussionParticipant(DiscussionParticipant $discussionParticipant): self
    {
        if ($this->discussionParticipants->removeElement($discussionParticipant)) {
            // set the owning side to null (unless already changed)
            if ($discussionParticipant->getParticipant() === $this) {
                $discussionParticipant->setParticipant(null);
            }
        }

        return $this;
    }

}
