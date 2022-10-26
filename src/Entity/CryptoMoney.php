<?php

namespace App\Entity;

use App\Repository\CryptoMoneyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

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

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setCrypto($this);
        }

        return $this;
    }

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

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getLogoLink(): ?string
    {
        return $this->logoLink;
    }

    public function setLogoLink(?string $logoLink): self
    {
        $this->logoLink = $logoLink;

        return $this;
    }


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
