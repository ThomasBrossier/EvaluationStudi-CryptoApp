<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Cette classe représente l'entité qui stock les transactions de ventes et d'achat.
 **/
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


    /**
     * Renvoie l'id de l'entité.
     * @return ?int
     **/
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Renvoie la date de l'enregistrement de la transaction.
     * @return ?DateTimeImmutable
     **/
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Initialise la date de la transaction.
     * @param DateTimeImmutable $createdAt
     * @return self
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    /**
     * Renvoie la quantité achetée ou vendue de la transaction.
     * @return ?float
     **/
    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    /**
     * Initialise la quantité achetée ou vendue lors de transaction.
     * @param float $quantity
     * @return self
     */
    public function setQuantity(float $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Renvoie la valeur unitaire de la transaction achetée ou vendue à une date donnée.
     * @return ?float
     **/
    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    /**
     * Initialise le prix unitaire lors de la transaction.
     * @param float $unitPrice
     * @return self
     */
    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * Renvoie la crypto liée à la transaction.
     * @return CryptoMoney|null
     */
    public function getCrypto(): ?CryptoMoney
    {
        return $this->crypto;
    }

    /**
     * Initialise la crypto de la transaction.
     * @param CryptoMoney|null $crypto
     * @return self
     */
    public function setCrypto(?CryptoMoney $crypto): self
    {
        $this->crypto = $crypto;

        return $this;
    }

    /**
     * Renvoie le type de transaction (Achat ou vente).
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Initialise le type de transaction (Achat ou vente).
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
