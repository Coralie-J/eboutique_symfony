<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\OneToMany(mappedBy: 'id_commande', targetEntity: CommandeLine::class)]
    private $commandeLines;

    #[ORM\ManyToOne(targetEntity: User2::class, inversedBy: 'commandes')]
    private $id_user;

    public function __construct()
    {
        $this->commandeLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|CommandeLine[]
     */
    public function getCommandeLines(): Collection
    {
        return $this->commandeLines;
    }

    public function addCommandeLine(CommandeLine $commandeLine): self
    {
        if (!$this->commandeLines->contains($commandeLine)) {
            $this->commandeLines[] = $commandeLine;
            $commandeLine->setIdCommande($this);
        }

        return $this;
    }

    public function removeCommandeLine(CommandeLine $commandeLine): self
    {
        if ($this->commandeLines->removeElement($commandeLine)) {
            // set the owning side to null (unless already changed)
            if ($commandeLine->getIdCommande() === $this) {
                $commandeLine->setIdCommande(null);
            }
        }

        return $this;
    }

    public function getIdUser(): ?User2
    {
        return $this->id_user;
    }

    public function setIdUser(?User2 $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }
}
