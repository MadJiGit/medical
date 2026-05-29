<?php

namespace App\Entity;

use App\Repository\SpecialistQuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialistQuestionRepository::class)]
#[ORM\Table(name: 'specialist_questions')]
class SpecialistQuestion
{
    public const STATUS_NEW = 'new';
    public const STATUS_ANSWERED = 'answered';
    public const STATUS_PUBLISHED = 'published';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Specialist $specialist = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'text')]
    private ?string $question = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isAnonymous = false;

    #[ORM\Column(length: 20, options: ['default' => 'new'])]
    private string $status = self::STATUS_NEW;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $answer = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $answeredBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $answeredAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPublic = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isAnonymous = false;
        $this->isPublic = false;
        $this->status = self::STATUS_NEW;
    }

    public function __toString(): string
    {
        return sprintf('%s — %s', $this->name ?? '', mb_substr($this->question ?? '', 0, 50));
    }

    public function getId(): ?int { return $this->id; }

    public function getSpecialist(): ?Specialist { return $this->specialist; }
    public function setSpecialist(?Specialist $specialist): static { $this->specialist = $specialist; return $this; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): static { $this->phone = $phone; return $this; }

    public function getQuestion(): ?string { return $this->question; }
    public function setQuestion(string $question): static { $this->question = $question; return $this; }

    public function isAnonymous(): bool { return $this->isAnonymous; }
    public function setIsAnonymous(bool $isAnonymous): static { $this->isAnonymous = $isAnonymous; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getAnswer(): ?string { return $this->answer; }
    public function setAnswer(?string $answer): static { $this->answer = $answer; return $this; }

    public function getAnsweredBy(): ?string { return $this->answeredBy; }
    public function setAnsweredBy(?string $answeredBy): static { $this->answeredBy = $answeredBy; return $this; }

    public function getAnsweredAt(): ?\DateTimeImmutable { return $this->answeredAt; }
    public function setAnsweredAt(?\DateTimeImmutable $answeredAt): static { $this->answeredAt = $answeredAt; return $this; }

    public function isPublic(): bool { return $this->isPublic; }
    public function setIsPublic(bool $isPublic): static { $this->isPublic = $isPublic; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
