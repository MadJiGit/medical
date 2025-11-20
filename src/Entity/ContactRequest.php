<?php

namespace App\Entity;

use App\Repository\ContactRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
class ContactRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contactRequests')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(targetEntity: Sample::class, mappedBy: 'contactRequest')]
    private ?Sample $sample = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $productId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    public function getSample(): ?Sample
    {
        return $this->sample;
    }

    public function setSample(?Sample $sample): static
    {
        // unset the owning side of the relation if necessary
        if ($sample === null && $this->sample !== null) {
            $this->sample->setContactRequest(null);
        }

        // set the owning side of the relation if necessary
        if ($sample !== null && $sample->getContactRequest() !== $this) {
            $sample->setContactRequest($this);
        }

        $this->sample = $sample;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    public function __toString(): string
    {
        return $this->user->getName();
    }

    public function isReturningCustomer(): bool
    {
        return $this->user ? $this->user->isReturningCustomer() : false;
    }

    public function getRequestCount(): int
    {
        return $this->user ? $this->user->getContactRequests()->count() : 0;
    }
}
