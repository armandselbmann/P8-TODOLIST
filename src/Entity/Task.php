<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Column(type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private datetime $createdAt;

    #[Assert\NotBlank(message: 'Vous devez saisir un titre.')]
    #[ORM\Column(type: 'string')]
    private string $title;

    #[Assert\NotBlank(message: 'Vous devez saisir du contenu.')]
    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'boolean')]
    private bool $isDone;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \Datetime();
        $this->isDone = false;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): Datetime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(datetime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function isDone(): bool
    {
        return $this->isDone;
    }

    public function toggle($flag): void
    {
        $this->isDone = $flag;
    }

    public function isIsDone(): ?bool
    {
        return $this->isDone;
    }

    public function setIsDone(bool $isDone): self
    {
        $this->isDone = $isDone;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
