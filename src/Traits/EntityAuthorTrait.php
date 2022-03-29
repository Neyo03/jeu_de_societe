<?php
namespace App\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

trait EntityAuthorTrait
{
    #[ORM\ManyToOne(targetEntity:User::class)]
    #[ORM\JoinColumn(nullable:false)]
    private $author;


    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}