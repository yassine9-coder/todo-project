<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(length=180, unique=true)
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Tache::class, mappedBy="user")
     */
    private $taches;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
    }

    /**
     * @return Collection|Tache[]
     */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): self
    {
        if (!$this->taches->contains($tache)) {
            $this->taches[] = $tache;
            $tache->setUser($this);
        }

        return $this;
    }

    public function removeTache(Tache $tache): self
    {
        if ($this->taches->removeElement($tache)) {
            // Set the owning side to null (unless already changed)
            if ($tache->getUser() === $this) {
                $tache->setUser(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        // Pas de rôles utilisés, donc renvoie un tableau vide
        return [];
    }

    public function setRoles(array $roles): static
    {
        // Pas besoin de gérer les rôles, cette méthode peut être ignorée ou supprimée
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear any sensitive data
    }
}
