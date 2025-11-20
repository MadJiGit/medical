<?php

namespace App\Entity;

use App\Repository\SampleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SampleRepository::class)]
class Sample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'samples')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $productId = null;

    #[ORM\Column]
    private ?bool $sentSample = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sampleManufacture = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $sentDate = null;

    #[ORM\Column]
    private ?bool $confirmReceivedSample = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $confirmReceivedDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $userFeedback = null;

    #[ORM\Column]
    private ?bool $returnedSample = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $returnedDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $returnedReason = null;

    #[ORM\OneToOne(inversedBy: 'sample')]
    private ?ContactRequest $contactRequest = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recipientName = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $recipientPhone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shippingAddress = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $shippingCity = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $shippingPostalCode = null;

    public function __toString(): string
    {
        return 'Sample #' . $this->id;
    }

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

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(?int $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    // Deprecated - use getProductId() instead
    public function getSampleType(): ?int
    {
        return $this->productId;
    }

    // Deprecated - use setProductId() instead
    public function setSampleType(?int $sampleType): static
    {
        $this->productId = $sampleType;

        return $this;
    }

    public function isSentSample(): ?bool
    {
        return $this->sentSample;
    }

    public function setSentSample(bool $sentSample): static
    {
        $this->sentSample = $sentSample;

        return $this;
    }

    public function getSampleManufacture(): ?string
    {
        return $this->sampleManufacture;
    }

    public function setSampleManufacture(?string $sampleManufacture): static
    {
        $this->sampleManufacture = $sampleManufacture;

        return $this;
    }

    public function getSentDate(): ?\DateTime
    {
        return $this->sentDate;
    }

    public function setSentDate(?\DateTime $sentDate): static
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    public function isConfirmReceivedSample(): ?bool
    {
        return $this->confirmReceivedSample;
    }

    public function setConfirmReceivedSample(bool $confirmReceivedSample): static
    {
        $this->confirmReceivedSample = $confirmReceivedSample;

        return $this;
    }

    public function getConfirmReceivedDate(): ?\DateTime
    {
        return $this->confirmReceivedDate;
    }

    public function setConfirmReceivedDate(?\DateTime $confirmReceivedDate): static
    {
        $this->confirmReceivedDate = $confirmReceivedDate;

        return $this;
    }

    public function isReturnedSample(): ?bool
    {
        return $this->returnedSample;
    }

    public function setReturnedSample(bool $returnedSample): static
    {
        $this->returnedSample = $returnedSample;

        return $this;
    }

    public function getReturnedDate(): ?\DateTime
    {
        return $this->returnedDate;
    }

    public function setReturnedDate(?\DateTime $returnedDate): static
    {
        $this->returnedDate = $returnedDate;

        return $this;
    }

    public function getReturnedReason(): ?string
    {
        return $this->returnedReason;
    }

    public function setReturnedReason(?string $returnedReason): static
    {
        $this->returnedReason = $returnedReason;

        return $this;
    }

    public function getUserFeedback(): ?string
    {
        return $this->userFeedback;
    }

    public function setUserFeedback(?string $userFeedback): static
    {
        $this->userFeedback = $userFeedback;

        return $this;
    }

    public function getContactRequest(): ?ContactRequest
    {
        return $this->contactRequest;
    }

    public function setContactRequest(?ContactRequest $contactRequest): static
    {
        $this->contactRequest = $contactRequest;

        return $this;
    }

    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    public function setRecipientName(?string $recipientName): static
    {
        $this->recipientName = $recipientName;

        return $this;
    }

    public function getRecipientPhone(): ?string
    {
        return $this->recipientPhone;
    }

    public function setRecipientPhone(?string $recipientPhone): static
    {
        $this->recipientPhone = $recipientPhone;

        return $this;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?string $shippingAddress): static
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getShippingCity(): ?string
    {
        return $this->shippingCity;
    }

    public function setShippingCity(?string $shippingCity): static
    {
        $this->shippingCity = $shippingCity;

        return $this;
    }

    public function getShippingPostalCode(): ?string
    {
        return $this->shippingPostalCode;
    }

    public function setShippingPostalCode(?string $shippingPostalCode): static
    {
        $this->shippingPostalCode = $shippingPostalCode;

        return $this;
    }
}
