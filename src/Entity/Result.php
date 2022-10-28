<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(
        type: 'float',
        message: 'La valeur {{ value }} n\'est pas valide. Il faut un nombre decimal.',
    )]
    private ?float $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
