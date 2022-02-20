<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 60)]
    private $nom;

    #[ORM\ManyToOne(targetEntity: Categorie::class, inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    private $type_categorie;

    #[ORM\Column(type: 'float')]
    private $prix_unitaire;

    #[ORM\Column(type: 'boolean')]
    private $disponibilite;

    #[ORM\OneToOne(mappedBy: 'id_produit', targetEntity: Media::class)]
    private $media;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\Column(type: 'string', length: 70)]
    private $interprete;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTypeCategorie(): ?Categorie
    {
        return $this->type_categorie;
    }

    public function setTypeCategorie(?Categorie $type_categorie): self
    {
        $this->type_categorie = $type_categorie;

        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->prix_unitaire;
    }

    public function setPrixUnitaire(float $prix_unitaire): self
    {
        $this->prix_unitaire = $prix_unitaire;

        return $this;
    }

    public function getDisponibilite(): ?bool
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(bool $disponibilite): self
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }


    public function getMedia(): Media
    {
        return $this->media;
    }

    public function setMedia(Media $media)
    {
        $this->media = $media;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description2): self
    {
        $this->description = $description2;

        return $this;
    }

    public function getInterprete(): ?string
    {
        return $this->interprete;
    }

    public function setInterprete(?string $interprete): self
    {
        $this->interprete = $interprete;

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }
}
