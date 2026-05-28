<?php

namespace App\Entity;

use App\Repository\VideoCategoryTranslationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoCategoryTranslationRepository::class)]
#[ORM\Table(name: 'video_category_translations')]
#[ORM\UniqueConstraint(name: 'uniq_video_cat_trans_locale', columns: ['category_id', 'locale'])]
class VideoCategoryTranslation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'translations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?VideoCategory $category = null;

    #[ORM\Column(length: 10)]
    private ?string $locale = null;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?VideoCategory
    {
        return $this->category;
    }

    public function setCategory(?VideoCategory $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): static
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }
}
