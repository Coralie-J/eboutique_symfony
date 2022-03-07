<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: "La source du media ne peut pas être vide")]
    #[Assert\Regex(
        pattern: '/(\.(?:jpg|jpeg|png))$/',
        match: true,
        message: "La source de l'image n'est pas au bon format",
    )]
    private $source;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\NotBlank(message: "Le texte alternatif ne peut pas être vide")]
    private $alt;

    #[ORM\OneToOne(targetEntity: Produit::class, inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: false)]
    private $id_produit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getIdProduit(): ?Produit
    {
        return $this->id_produit;
    }

    public function setIdProduit(?Produit $id_produit): self
    {
        $this->id_produit = $id_produit;

        return $this;
    }
}
