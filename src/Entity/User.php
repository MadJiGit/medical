<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private ?bool $isActive = false;

    #[ORM\Column]
    private ?bool $isBanned = false;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $confirmationToken = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $tokenExpiresAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Sample>
     */
    #[ORM\OneToMany(targetEntity: Sample::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $samples;

    /**
     * @var Collection<int, ContactRequest>
     */
    #[ORM\OneToMany(targetEntity: ContactRequest::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $contactRequests;

    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->contactRequests = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->email ?? '';
    }

    public function isReturningCustomer(): bool
    {
        return $this->contactRequests->count() > 1 || $this->samples->count() > 0;
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

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): static
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): static
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function getTokenExpiresAt(): ?\DateTimeImmutable
    {
        return $this->tokenExpiresAt;
    }

    public function setTokenExpiresAt(?\DateTimeImmutable $tokenExpiresAt): static
    {
        $this->tokenExpiresAt = $tokenExpiresAt;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Sample>
     */
    public function getSamples(): Collection
    {
        return $this->samples;
    }

    public function addSample(Sample $sample): static
    {
        if (!$this->samples->contains($sample)) {
            $this->samples->add($sample);
            $sample->setUser($this);
        }

        return $this;
    }

    public function removeSample(Sample $sample): static
    {
        if ($this->samples->removeElement($sample)) {
            // set the owning side to null (unless already changed)
            if ($sample->getUser() === $this) {
                $sample->setUser(null);
            }
        }

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email;
        // TODO: Implement getUserIdentifier() method.
    }

    /**
     * @return Collection<int, ContactRequest>
     */
    public function getContactRequests(): Collection
    {
        return $this->contactRequests;
    }

    public function addContactRequest(ContactRequest $contactRequest): static
    {
        if (!$this->contactRequests->contains($contactRequest)) {
            $this->contactRequests->add($contactRequest);
            $contactRequest->setUser($this);
        }

        return $this;
    }

    public function removeContactRequest(ContactRequest $contactRequest): static
    {
        if ($this->contactRequests->removeElement($contactRequest)) {
            // set the owning side to null (unless already changed)
            if ($contactRequest->getUser() === $this) {
                $contactRequest->setUser(null);
            }
        }

        return $this;
    }
}
