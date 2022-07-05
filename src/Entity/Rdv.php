<?php

namespace App\Entity;

use App\Repository\RdvRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RdvRepository::class)
 * 
 */
class Rdv
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Veuillez saisir un message")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Prestations::class, inversedBy="rdvs")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Veuillez choisir une prestation")
     */
    private $prestations;

    /**
     * @Assert\NotBlank(message="Veuillez choisir un type de rdv")
     * @ORM\ManyToOne(targetEntity=TypeRdv::class, inversedBy="rdvs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $TypeRdv;




    /**
     * @Assert\NotBlank(message="Veuillez choisir une horaire")
     * @ORM\OneToOne(targetEntity=PlageHoraire::class, inversedBy="rdv", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $horaire;

    /**
     * @ORM\ManyToOne(targetEntity=Clients::class, inversedBy="rdvs")
     * 
     */
    private $client;

    /**
     * @ORM\Column(type="boolean")
     */
    private $rdv_valide = 0;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }




    public function getPrestations(): ?Prestations
    {
        return $this->prestations;
    }

    public function setPrestations(?Prestations $prestations): self
    {
        $this->prestations = $prestations;

        return $this;
    }


    public function getTypeRdv(): ?TypeRdv
    {
        return $this->TypeRdv;
    }

    public function setTypeRdv(?TypeRdv $TypeRdv): self
    {
        $this->TypeRdv = $TypeRdv;

        return $this;
    }

    public function getHoraire(): ?PlageHoraire
    {
        return $this->horaire;
    }

    public function setHoraire(?PlageHoraire $horaire): self
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getRdvValide(): ?bool
    {
        return $this->rdv_valide;
    }

    public function setRdvValide(bool $rdv_valide): self
    {
        $this->rdv_valide = $rdv_valide;

        return $this;
    }



}