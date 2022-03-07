<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "L'adresse ne peut pas être vide")]
    private $adresse;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: "Le code postal ne peut pas être vide")]
    #[Assert\Length(
        min: 5,
        max: 5,
        exactMessage: "Le code postal doit contenir 5 chiffres"
    )]
    private $code_postal;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\NotBlank(message: "La ville ne peut pas être vide")]
    private $ville;

    #[ORM\ManyToOne(targetEntity: User2::class, inversedBy: 'adresses')]
    private $id_user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?int
    {
        return $this->code_postal;
    }

    public function setCodePostal(int $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

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
