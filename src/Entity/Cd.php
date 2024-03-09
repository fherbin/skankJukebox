<?php

namespace App\Entity;

use App\Repository\CdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CdRepository::class)]
class Cd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $artist = null;

    #[ORM\OneToMany(targetEntity: Track::class, mappedBy: 'cd', cascade: ['persist'], orphanRemoval: true)]
    private Collection $tracks;

    #[ORM\OneToOne(mappedBy: 'Cd')]
    private ?Slot $slot = null;

    public function __construct()
    {
        $this->tracks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    /** @return Collection<int, Track> */
    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function addTrack(Track $track): static
    {
        if (!$this->tracks->contains($track)) {
            $this->tracks->add($track);
            $track->setCd($this);
        }

        return $this;
    }

    public function removeTrack(Track $track): static
    {
        if ($this->tracks->removeElement($track)) {
            // set the owning side to null (unless already changed)
            if ($track->getCd() === $this) {
                $track->setCd(null);
            }
        }

        return $this;
    }

    public function getSlot(): ?Slot
    {
        return $this->slot;
    }

    public function setSlot(?Slot $slot): static
    {
        // unset the owning side of the relation if necessary
        if ($slot === null && $this->slot !== null) {
            $this->slot->setCd(null);
        }

        // set the owning side of the relation if necessary
        if ($slot !== null && $slot->getCd() !== $this) {
            $slot->setCd($this);
        }

        $this->slot = $slot;

        return $this;
    }
}
