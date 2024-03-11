<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SlotRepository::class)]
class Slot
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'slot', cascade: ['persist'], orphanRemoval: true)]
    private ?Cd $Cd = null;

    #[ORM\Column(unique: true)]
    private ?int $number = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCd(): ?Cd
    {
        return $this->Cd;
    }

    public function setCd(?Cd $Cd): static
    {
        $this->Cd = $Cd;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }
}
