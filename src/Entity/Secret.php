<?php

namespace App\Entity;

use App\Repository\SecretRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Attributes\Property;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraint as CustomAssert;

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
    #[Property(
        property: 'hash',
        description: 'Unique hash to identify the secrets',
        type: 'string'
    )]
    private ?string $hash = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Property(
        property: 'secret',
        description: 'The secret itself',
        type: 'string'
    )]
    private ?string $secretText = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Property(
        property: 'createdAt',
        description: 'The date and time of the creation',
        type: 'string',
        format: 'date-time'
    )]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[CustomAssert\DateTimeOrNull]
    #[Property(
        property: 'expiresAt',
        description: 'The secret cannot be reached after this time',
        type: 'string',
        format: 'date-time'
    )]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column]
    #[Assert\GreaterThan(0)]
    #[Property(
        property: 'remainingViews',
        description: 'How many times the secret can be viewed',
        type: 'integer',
        format: 'int32'
    )]
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


    /**
     * Sets the <b>expiresAt</b> property. When the parameter is zero the secret never will expire,
     * otherwise it's expired in x minutes.
     *
     * @param int $timeInMinutes Zero or a positive number
     * @return $this
     */
    public function setExpirationTime(int $timeInMinutes): static
    {
        if ($timeInMinutes <= 0) {
            $this->expiresAt = null;
            return $this;
        }

        $this->expiresAt = new \DateTimeImmutable("+ {$timeInMinutes} minutes");
        return $this;
    }
}
