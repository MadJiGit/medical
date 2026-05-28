<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[ORM\Table(name: 'blog_posts')]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BlogCategory $category = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: BlogPostTranslation::class, mappedBy: 'post', cascade: ['persist', 'remove'], orphanRemoval: true)]
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

    public function getCategory(): ?BlogCategory
    {
        return $this->category;
    }

    public function setCategory(?BlogCategory $category): static
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /** @return Collection<int, BlogPostTranslation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $locale): ?BlogPostTranslation
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

    private function getOrCreateTranslation(string $locale): BlogPostTranslation
    {
        foreach ($this->translations as $t) {
            if ($t->getLocale() === $locale) {
                return $t;
            }
        }
        $t = new BlogPostTranslation();
        $t->setLocale($locale);
        $t->setPost($this);
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

    public function getExcerptBg(): ?string
    {
        return $this->getTranslation('bg')?->getExcerpt();
    }

    public function setExcerptBg(?string $excerpt): static
    {
        $this->getOrCreateTranslation('bg')->setExcerpt($excerpt);
        return $this;
    }

    public function getExcerptEn(): ?string
    {
        return $this->getTranslation('en')?->getExcerpt();
    }

    public function setExcerptEn(?string $excerpt): static
    {
        $this->getOrCreateTranslation('en')->setExcerpt($excerpt);
        return $this;
    }

    public function getContentBg(): ?string
    {
        return $this->getTranslation('bg')?->getContent();
    }

    public function setContentBg(?string $content): static
    {
        $this->getOrCreateTranslation('bg')->setContent($content);
        return $this;
    }

    public function getContentEn(): ?string
    {
        return $this->getTranslation('en')?->getContent();
    }

    public function setContentEn(?string $content): static
    {
        $this->getOrCreateTranslation('en')->setContent($content);
        return $this;
    }
}
