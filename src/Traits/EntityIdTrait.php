<?php
namespace App\Traits;

use Ramsey\Uuid\Rfc4122\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

trait EntityIdTrait
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}

