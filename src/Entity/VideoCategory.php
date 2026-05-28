<?php

namespace App\Entity;

use App\Repository\VideoCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoCategoryRepository::class)]
#[ORM\Table(name: 'video_categories')]
class VideoCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $sortOrder = 0;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\OneToMany(targetEntity: Video::class, mappedBy: 'category')]
    private Collection $videos;

    #[ORM\OneToMany(targetEntity: VideoCategoryTranslation::class, mappedBy: 'category', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct()
    {
        $this->videos = new ArrayCollection();
        $this->translations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTranslation('bg')?->getLabel() ?? '';
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

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
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

    /** @return Collection<int, Video> */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): static
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setCategory($this);
        }
        return $this;
    }

    public function removeVideo(Video $video): static
    {
        if ($this->videos->removeElement($video)) {
            if ($video->getCategory() === $this) {
                $video->setCategory(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, VideoCategoryTranslation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $locale): ?VideoCategoryTranslation
    {
        $fallback = null;
        foreach ($this->translations as $t) {
            if ($t->getLocale() === $locale) {
                return $t;
            }
            if ($t->getLocale() === 'bg') {
                $fallback = $t;
            }
        }
        return $fallback;
    }

    private function getOrCreateTranslation(string $locale): VideoCategoryTranslation
    {
        foreach ($this->translations as $t) {
            if ($t->getLocale() === $locale) {
                return $t;
            }
        }
        $t = new VideoCategoryTranslation();
        $t->setLocale($locale);
        $t->setCategory($this);
        $this->translations->add($t);
        return $t;
    }

    // Virtual getters/setters — backward compatibility for EasyAdmin and import commands
    public function getLabelBg(): ?string
    {
        return $this->getTranslation('bg')?->getLabel();
    }

    public function setLabelBg(string $label): static
    {
        $this->getOrCreateTranslation('bg')->setLabel($label);
        return $this;
    }

    public function getLabelEn(): ?string
    {
        return $this->getTranslation('en')?->getLabel();
    }

    public function setLabelEn(string $label): static
    {
        $this->getOrCreateTranslation('en')->setLabel($label);
        return $this;
    }
}
