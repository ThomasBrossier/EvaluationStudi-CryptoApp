<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Cette classe représente l'entité qui stock les données journalières de la valeur actuelle des investissements.
 **/

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?DateTimeImmutable $CreatedAt = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'float',
        message: 'La valeur {{ value }} n\'est pas valide. Il faut un nombre decimal.',
    )]
    private ?float $amount = null;


    /**
     * Renvoie l'id de l'entité.
     * @return ?int
     **/
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Renvoie la date de l'enregistrement de la valeur.
     * @return ?DateTimeImmutable
     **/
    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    /**
     * Initialise la date de l'enregistrement.
     * @param DateTimeImmutable $CreatedAt
     * @return self
     */
    public function setCreatedAt(DateTimeImmutable $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }


    /**
     * Renvoie le montant enregistré.
     * @return ?float
     **/
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * Initialise le montant de l'enregistrement.
     * @param float $amount
     * @return self
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
