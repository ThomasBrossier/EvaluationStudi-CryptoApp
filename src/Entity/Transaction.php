<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Assert\Type(
        type: 'float',
        message: 'La valeur {{ value }} n\'est pas valide. Il faut un nombre decimale.',
    )]
    private ?float $quantity = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Assert\Type(
        type: 'float',
        message: 'La valeur {{ value }} n\'est pas valide. Il faut un nombre decimal.',
    )]
    private ?float $unitPrice = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'transactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?CryptoMoney $crypto = null;

    #[ORM\Column(length: 10)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getCrypto(): ?CryptoMoney
    {
        return $this->crypto;
    }

    public function setCrypto(?CryptoMoney $crypto): self
    {
        $this->crypto = $crypto;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
