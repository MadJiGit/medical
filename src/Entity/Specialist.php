<?php

namespace App\Entity;

use App\Repository\SpecialistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialistRepository::class)]
#[ORM\Table(name: 'specialists')]
class Specialist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialtyBg = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $specialtyEn = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bioBg = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bioEn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $sortOrder = 0;

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSpecialtyBg(): ?string
    {
        return $this->specialtyBg;
    }

    public function setSpecialtyBg(?string $specialtyBg): static
    {
        $this->specialtyBg = $specialtyBg;
        return $this;
    }

    public function getSpecialtyEn(): ?string
    {
        return $this->specialtyEn;
    }

    public function setSpecialtyEn(?string $specialtyEn): static
    {
        $this->specialtyEn = $specialtyEn;
        return $this;
    }

    public function getBioBg(): ?string
    {
        return $this->bioBg;
    }

    public function setBioBg(?string $bioBg): static
    {
        $this->bioBg = $bioBg;
        return $this;
    }

    public function getBioEn(): ?string
    {
        return $this->bioEn;
    }

    public function setBioEn(?string $bioEn): static
    {
        $this->bioEn = $bioEn;
        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;
        return $this;
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

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}
