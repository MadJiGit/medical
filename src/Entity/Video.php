<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\Table(name: 'videos')]
class Video
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'videos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VideoCategory $category = null;

    #[ORM\Column(length: 20)]
    private ?string $sourceType = null;

    #[ORM\Column(length: 255)]
    private ?string $sourceId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $thumbnail = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $sortOrder = 0;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: VideoTranslation::class, mappedBy: 'video', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isActive = true;
        $this->translations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTranslation('bg')?->getTitle() ?? '';
    }

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

    public function getSourceType(): ?string
    {
        return $this->sourceType;
    }

    public function setSourceType(string $sourceType): static
    {
        $this->sourceType = $sourceType;
        return $this;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setSourceId(string $sourceId): static
    {
        $this->sourceId = $sourceId;
        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /** @return Collection<int, VideoTranslation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $locale): ?VideoTranslation
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

    private function getOrCreateTranslation(string $locale): VideoTranslation
    {
        foreach ($this->translations as $t) {
            if ($t->getLocale() === $locale) {
                return $t;
            }
        }
        $t = new VideoTranslation();
        $t->setLocale($locale);
        $t->setVideo($this);
        $this->translations->add($t);
        return $t;
    }

    // Virtual getters/setters — backward compatibility for EasyAdmin and import commands
    public function getTitleBg(): ?string
    {
        return $this->getTranslation('bg')?->getTitle();
    }

    public function setTitleBg(string $title): static
    {
        $this->getOrCreateTranslation('bg')->setTitle($title);
        return $this;
    }

    public function getTitleEn(): ?string
    {
        return $this->getTranslation('en')?->getTitle();
    }

    public function setTitleEn(string $title): static
    {
        $this->getOrCreateTranslation('en')->setTitle($title);
        return $this;
    }

    public function getThumbnailUrl(): string
    {
        if ($this->thumbnail) {
            return $this->thumbnail;
        }
        if ($this->sourceType === 'youtube') {
            return 'https://img.youtube.com/vi/' . $this->sourceId . '/hqdefault.jpg';
        }
        return '';
    }

    public function getEmbedUrl(): string
    {
        return match($this->sourceType) {
            'youtube' => 'https://www.youtube.com/embed/' . $this->sourceId . '?autoplay=1',
            'vimeo'   => 'https://player.vimeo.com/video/' . $this->sourceId . '?autoplay=1',
            default   => '/videos/' . $this->sourceId,
        };
    }
}
