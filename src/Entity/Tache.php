<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 */
class Tache
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups("tache:read")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups("tache:read")
     */
    private ?int $userId = null; // Champ pour stocker l'ID de l'utilisateur

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tache:read", "tache:write"})
     */
    private ?string $titre = null;

    /**
     * @ORM\Column(type="text")
     * @Groups({"tache:read", "tache:write"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tache:read", "tache:write"})
     */
    private ?bool $terminee = null;

    // Getters and Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function isTerminee(): ?bool
    {
        return $this->terminee;
    }

    public function setTerminee(bool $terminee): self
    {
        $this->terminee = $terminee;
        return $this;
    }
}
