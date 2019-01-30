<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RendezVousRepository")
 */
class RendezVous
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"patient"})
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     * @Groups({"patient"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"patient"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $permission;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $accompagnant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $choix;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $motifRefus;

    /**
     * @ORM\Column(type="date", nullable=true, nullable=true)
     * @Groups({"patient"})
     */
    private $date_repro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $type_repro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $permission_repro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $accompagnant_repro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $choix_repro;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $MotifRefus_repro;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="rendezVous")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rendezVous"})
     */
    private $patient;

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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPermission(): ?string
    {
        return $this->permission;
    }

    public function setPermission(?string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    public function getAccompagnant(): ?string
    {
        return $this->accompagnant;
    }

    public function setAccompagnant(?string $accompagnant): self
    {
        $this->accompagnant = $accompagnant;

        return $this;
    }

    public function getChoix(): ?string
    {
        return $this->choix;
    }

    public function setChoix(?string $choix): self
    {
        $this->choix = $choix;

        return $this;
    }

    public function getMotifRefus(): ?string
    {
        return $this->motifRefus;
    }

    public function setMotifRefus(?string $motifRefus): self
    {
        $this->motifRefus = $motifRefus;

        return $this;
    }

    public function getDateRepro(): ?\DateTimeInterface
    {
        return $this->date_repro;
    }

    public function setDateRepro(?\DateTimeInterface $date_repro): self
    {
        $this->date_repro = $date_repro;

        return $this;
    }

    public function getTypeRepro(): ?string
    {
        return $this->type_repro;
    }

    public function setTypeRepro(string $type_repro): self
    {
        $this->type_repro = $type_repro;

        return $this;
    }

    public function getPermissionRepro(): ?string
    {
        return $this->permission_repro;
    }

    public function setPermissionRepro(?string $permission_repro): self
    {
        $this->permission_repro = $permission_repro;

        return $this;
    }

    public function getAccompagnantRepro(): ?string
    {
        return $this->accompagnant_repro;
    }

    public function setAccompagnantRepro(?string $accompagnant_repro): self
    {
        $this->accompagnant_repro = $accompagnant_repro;

        return $this;
    }

    public function getChoixRepro(): ?string
    {
        return $this->choix_repro;
    }

    public function setChoixRepro(?string $choix_repro): self
    {
        $this->choix_repro = $choix_repro;

        return $this;
    }

    public function getMotifRefusRepro(): ?string
    {
        return $this->MotifRefus_repro;
    }

    public function setMotifRefusRepro(?string $MotifRefus_repro): self
    {
        $this->MotifRefus_repro = $MotifRefus_repro;

        return $this;
    }

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): self
    {
        $this->patient = $patient;

        return $this;
    }
}
