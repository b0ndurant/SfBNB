<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VisitorRepository")
 */
class Visitor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hashIp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHashIp(): ?string
    {
        return $this->hashIp;
    }

    public function setHashIp(string $hashIp): self
    {
        $this->hashIp = $hashIp;

        return $this;
    }
}
