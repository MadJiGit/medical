<?php

namespace App\Entity;

use App\Repository\BlogCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogCategoryRepository::class)]
#[ORM\Table(name: 'blog_categories')]
class BlogCategory
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

    #[ORM\OneToMany(targetEntity: BlogPost::class, mappedBy: 'category')]
    private Collection $posts;

    #[ORM\OneToMany(targetEntity: BlogCategoryTranslation::class, mappedBy: 'category', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $translations;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
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

    /** @return Collection<int, BlogPost> */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(BlogPost $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCategory($this);
        }
        return $this;
    }

    public function removePost(BlogPost $post): static
    {
        if ($this->posts->removeElement($post)) {
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, BlogCategoryTranslation> */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function getTranslation(string $locale): ?BlogCategoryTranslation
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

    private function getOrCreateTranslation(string $locale): BlogCategoryTranslation
    {
        foreach ($this->translations as $t) {
            if ($t->getLocale() === $locale) {
                return $t;
            }
        }
        $t = new BlogCategoryTranslation();
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
