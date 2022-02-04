<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"patient"})
     */
    private $date;

    /**
     * @ORM\Column(type="time", nullable=true)
     * @Groups({"patient"})
     */
    private $heure;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $thematique;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $accompagnant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $etat;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $motifRefus;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="rendezVous")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"rendezVous", "slot"})
     */
    private $patient;

    /**
     * @ORM\ManyToOne(targetEntity=Slot::class, inversedBy="rendezVous", fetch="EAGER")
     */
    private $slot;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $send;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
    
    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(?\DateTimeInterface $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getThematique(): ?string
    {
        return $this->thematique;
    }

    public function setThematique(?string $thematique): self
    {
        $this->thematique = $thematique;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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

    public function getMotifRefus(): ?string
    {
        return $this->motifRefus;
    }

    public function setMotifRefus(?string $motifRefus): self
    {
        $this->motifRefus = $motifRefus;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getSlot(): ?Slot
    {
        return $this->slot;
    }

    public function setSlot(?Slot $slot): self
    {
        $this->slot = $slot;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(?string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getSend(): ?string
    {
        return $this->send;
    }

    public function setSend(?string $send): self
    {
        $this->send = $send;

        return $this;
    }
}
