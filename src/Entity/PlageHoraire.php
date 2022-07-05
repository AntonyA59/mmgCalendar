<?php

namespace App\Entity;

use DateTimeInterface;
use App\Repository\PlageHoraireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=PlageHoraireRepository::class)
 * @UniqueEntity(
 * fields={"horaire"},
 * errorPath="horaire",
 * message="Cet horaire est dejà utilisé."
 * )
 */
class PlageHoraire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotNull(message="Veuillez seléctionner une horaire")
     */
    private $horaire;



    /**
     * @ORM\Column(type="boolean")
     */
    private $horairePrise = 0;

    /**
     * @ORM\OneToOne(targetEntity=Rdv::class, mappedBy="horaire", cascade={"persist", "remove"})
     */
    private $rdv;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHoraire(): ?\DateTimeInterface
    {
        return $this->horaire;
    }

    public function setHoraire(\DateTimeInterface $horaire): self
    {

        $this->horaire = $horaire;

        return $this;
    }

    public function getHorairePrise(): ?bool
    {
        return $this->horairePrise;
    }

    public function setHorairePrise(bool $horairePrise): self
    {
        $this->horairePrise = $horairePrise;

        return $this;
    }

    public function getRdv(): ?Rdv
    {
        return $this->rdv;
    }

    public function setRdv(Rdv $rdv): self
    {
        // set the owning side of the relation if necessary
        if ($rdv->getHoraire() !== $this) {
            $rdv->setHoraire($this);
        }

        $this->rdv = $rdv;

        return $this;
    }











}