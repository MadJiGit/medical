<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $nameBg = null;

    #[ORM\Column(length: 255)]
    private ?string $nameEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionBg = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $shortDescriptionEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionBg = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEn = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $featuresBg = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $featuresEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $suitableForBg = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $suitableForEn = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $howToUseBg = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $howToUseEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $manufacturer = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nhifCode = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private ?int $quantity = 0;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private ?bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->quantity = 0;
        $this->isActive = true;
    }

    public function __toString(): string
    {
        return $this->nameBg ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getNameBg(): ?string
    {
        return $this->nameBg;
    }

    public function setNameBg(string $nameBg): static
    {
        $this->nameBg = $nameBg;
        return $this;
    }

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): static
    {
        $this->nameEn = $nameEn;
        return $this;
    }

    public function getShortDescriptionBg(): ?string
    {
        return $this->shortDescriptionBg;
    }

    public function setShortDescriptionBg(?string $shortDescriptionBg): static
    {
        $this->shortDescriptionBg = $shortDescriptionBg;
        return $this;
    }

    public function getShortDescriptionEn(): ?string
    {
        return $this->shortDescriptionEn;
    }

    public function setShortDescriptionEn(?string $shortDescriptionEn): static
    {
        $this->shortDescriptionEn = $shortDescriptionEn;
        return $this;
    }

    public function getDescriptionBg(): ?string
    {
        return $this->descriptionBg;
    }

    public function setDescriptionBg(?string $descriptionBg): static
    {
        $this->descriptionBg = $descriptionBg;
        return $this;
    }

    public function getDescriptionEn(): ?string
    {
        return $this->descriptionEn;
    }

    public function setDescriptionEn(?string $descriptionEn): static
    {
        $this->descriptionEn = $descriptionEn;
        return $this;
    }

    public function getFeaturesBg(): ?array
    {
        return $this->featuresBg;
    }

    public function setFeaturesBg(?array $featuresBg): static
    {
        $this->featuresBg = $featuresBg;
        return $this;
    }

    public function getFeaturesEn(): ?array
    {
        return $this->featuresEn;
    }

    public function setFeaturesEn(?array $featuresEn): static
    {
        $this->featuresEn = $featuresEn;
        return $this;
    }

    public function getSuitableForBg(): ?string
    {
        return $this->suitableForBg;
    }

    public function setSuitableForBg(?string $suitableForBg): static
    {
        $this->suitableForBg = $suitableForBg;
        return $this;
    }

    public function getSuitableForEn(): ?string
    {
        return $this->suitableForEn;
    }

    public function setSuitableForEn(?string $suitableForEn): static
    {
        $this->suitableForEn = $suitableForEn;
        return $this;
    }

    public function getHowToUseBg(): ?string
    {
        return $this->howToUseBg;
    }

    public function setHowToUseBg(?string $howToUseBg): static
    {
        $this->howToUseBg = $howToUseBg;
        return $this;
    }

    public function getHowToUseEn(): ?string
    {
        return $this->howToUseEn;
    }

    public function setHowToUseEn(?string $howToUseEn): static
    {
        $this->howToUseEn = $howToUseEn;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    public function getNhifCode(): ?string
    {
        return $this->nhifCode;
    }

    public function setNhifCode(?string $nhifCode): static
    {
        $this->nhifCode = $nhifCode;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
