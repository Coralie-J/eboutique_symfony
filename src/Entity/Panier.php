<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'paniers')]
    private $id_user;

    #[ORM\OneToMany(mappedBy: 'id_panier', targetEntity: PanierLine::class)]
    private $panierLines;

    public function __construct()
    {
        $this->panierLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->id_user;
    }

    public function setIdUser(?User $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    /**
     * @return Collection|PanierLine[]
     */
    public function getPanierLines(): Collection
    {
        return $this->panierLines;
    }

    public function addPanierLine(PanierLine $panierLine): self
    {
        if (!$this->panierLines->contains($panierLine)) {
            $this->panierLines[] = $panierLine;
            $panierLine->setIdPanier($this);
        }

        return $this;
    }

    public function removePanierLine(PanierLine $panierLine): self
    {
        if ($this->panierLines->removeElement($panierLine)) {
            // set the owning side to null (unless already changed)
            if ($panierLine->getIdPanier() === $this) {
                $panierLine->setIdPanier(null);
            }
        }

        return $this;
    }
}
