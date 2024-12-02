<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\SecretRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SecretRepository::class)]
#[UniqueEntity('hash')]
class Secret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    private ?string $hash = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $secretText = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?int $remainingViews = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getSecretText(): ?string
    {
        return $this->secretText;
    }

    public function setSecretText(string $secretText): static
    {
        $this->secretText = $secretText;

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

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getRemainingViews(): ?int
    {
        return $this->remainingViews;
    }

    public function setRemainingViews(int $remainingViews): static
    {
        $this->remainingViews = $remainingViews;

        return $this;
    }
}
