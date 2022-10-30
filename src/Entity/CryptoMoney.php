<?php

namespace App\Entity;

use App\Repository\CryptoMoneyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;


/**
 * Cette classe représente l'entité qui stock les données d'une cryptomonnaie.
 **/

#[ORM\Entity(repositoryClass: CryptoMoneyRepository::class)]
class CryptoMoney
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(min:2, max:150)]
    private ?string $title = null;

    #[ORM\OneToMany(mappedBy: 'crypto', targetEntity: Transaction::class, cascade: ['persist'])]
    private Collection $transactions;

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank]
    #[Assert\Length(min:3, max:5)]
    private ?string $symbol = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min:10, max:255)]
    private ?string $logoLink = null;

    /**
     * Lorsque l'entité est construite, une collection de transactions est initialisé.
     **/
    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    /**
     * Renvoie l'id de l'entité.
     * @return ?int
     **/
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Renvoie le titre (le nom de la crypto ) de l'entité.
     * @return ?string
     **/
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Initialise le titre (le nom de la crypto) de l'entité.
     * @param string $title
     * @return self
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Renvoie les transactions liées à l'entité.
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    /**
     * Ajoute une transaction à la collection liée à l'entité.
     * @param Transaction $transaction
     * @return self
     */
    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setCrypto($this);
        }

        return $this;
    }

    /**
     * Supprime une transaction donnée de la collection de l'entité.
     * @param Transaction $transaction
     * @return self
     */
    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getCrypto() === $this) {
                $transaction->setCrypto(null);
            }
        }

        return $this;
    }


    /**
     * Renvoie le symbol de l'entité.
     * @return ?string
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * Permet d'initialiser le symbol de l'entité.
     * @param string $symbol
     * @return self
     */
    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Renvoie le lien du logo de l'entité.
     * @return ?string
     */
    public function getLogoLink(): ?string
    {
        return $this->logoLink;
    }

    /**
     * Permet d'initialiser le lien du logo de l'entité.
     * @param string|null $logoLink
     * @return self
     */
    public function setLogoLink(?string $logoLink): self
    {
        $this->logoLink = $logoLink;

        return $this;
    }

    /**
     * Permet d'obtenir la quantité totale en stock de la crypto (l'entité).
     * @return float
     */
    public function getTotalQuantity(): float
    {
        $quantity = 0;
        foreach ($this->transactions as $transaction ){
            if($transaction->getType() === "purchase"){
                $quantity += $transaction->getQuantity();
            }else{
                $quantity -= $transaction->getQuantity();
            }
        }

        return $quantity;
    }

    /**
     * Permet d'obtenir la valeur totale des achats de la crypto (l'entité).
     * @return float
     */
    public function getTotalSpent(): float
    {
        $amount = 0;
        foreach ($this->transactions as $transaction ){
            if($transaction->getType() === "purchase"){
                $amount = $amount +  $transaction->getQuantity() * $transaction->getUnitPrice() ;
            }else{
                $amount = $amount - $transaction->getQuantity() * $transaction->getUnitPrice() ;
            }
        }

        return $amount;
    }
}
