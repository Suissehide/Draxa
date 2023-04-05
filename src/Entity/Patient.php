<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PatientRepository")
 */
class Patient
{
    /**
     * @Groups({"slot"})
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $observ;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $divers;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="text", nullable=true)
     */
    private $notes;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sexe;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel1;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tel2;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $distance;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etude;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profession;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activite;

/**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $diagnostic;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $dedate;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $orientation;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etpdecision;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $progetp;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $precisions;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $precisionsperso;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $objectif;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $dentree;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motif;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etp;

    /**
     * @Groups({"patient", "rendezVous"})
     * @ORM\OneToMany(targetEntity="App\Entity\RendezVous", mappedBy="patient", cascade={"persist", "remove"}, orphanRemoval=true, indexBy="id", fetch="EAGER")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $rendezVous;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $soignantReferent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $offre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\OneToOne(targetEntity=DiagnosticEducatif::class, cascade={"persist", "remove"})
     */
    private $diagnosticEducatif;

    public function __construct()
    {
        $this->rendezVous = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObserv(): ?string
    {
        return $this->observ;
    }

    public function setObserv(?string $observ): self
    {
        $this->observ = $observ;

        return $this;
    }
    
    public function getDivers(): ?string
    {
        return $this->divers;
    }

    public function setDivers(?string $divers): self
    {
        $this->divers = $divers;

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

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getEtp(): ?string
    {
        return $this->etp;
    }

    public function setEtp(string $etp): self
    {
        $this->etp = $etp;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDentree(): ?\DateTimeInterface
    {
        return $this->dentree;
    }

    public function setDentree(?\DateTimeInterface $dentree): self
    {
        $this->dentree = $dentree;

        return $this;
    }

    public function getTel1(): ?string
    {
        return $this->tel1;
    }

    public function setTel1(string $tel1): self
    {
        $this->tel1 = $tel1;

        return $this;
    }

    public function getTel2(): ?string
    {
        return $this->tel2;
    }

    public function setTel2(?string $tel2): self
    {
        $this->tel2 = $tel2;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getEtude(): ?string
    {
        return $this->etude;
    }

    public function setEtude(string $etude): self
    {
        $this->etude = $etude;

        return $this;
    }

    public function getProfession(): ?string
    {
        return $this->profession;
    }

    public function setProfession(string $profession): self
    {
        $this->profession = $profession;

        return $this;
    }

    public function getActivite(): ?string
    {
        return $this->activite;
    }

    public function setActivite(string $activite): self
    {
        $this->activite = $activite;

        return $this;
    }

    public function getDiagnostic(): ?string
    {
        return $this->diagnostic;
    }

    public function setDiagnostic(string $diagnostic): self
    {
        $this->diagnostic = $diagnostic;

        return $this;
    }

    public function getDedate(): ?\DateTimeInterface
    {
        return $this->dedate;
    }

    public function setDedate(?\DateTimeInterface $dedate): self
    {
        $this->dedate = $dedate;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(?string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }

    public function getEtpdecision(): ?string
    {
        return $this->etpdecision;
    }

    public function setEtpdecision(?string $etpdecision): self
    {
        $this->etpdecision = $etpdecision;

        return $this;
    }

    public function getPrecisions(): ?string
    {
        return $this->precisions;
    }

    public function setPrecisions(?string $precisions): self
    {
        $this->precisions = $precisions;

        return $this;
    }

    public function getProgetp(): ?string
    {
        return $this->progetp;
    }

    public function setProgetp(?string $progetp): self
    {
        $this->progetp = $progetp;

        return $this;
    }

    public function getPrecisionsperso(): ?string
    {
        return $this->precisionsperso;
    }

    public function setPrecisionsperso(?string $precisionsperso): self
    {
        $this->precisionsperso = $precisionsperso;

        return $this;
    }

    public function getObjectif(): ?string
    {
        return $this->objectif;
    }

    public function setObjectif(?string $objectif): self
    {
        $this->objectif = $objectif;

        return $this;
    }

    /**
     * @return Collection|RendezVous[]
     */
    public function getRendezVous(): Collection
    {
        return $this->rendezVous;
    }

    public function addRendezVou(RendezVous $rendezVous): self
    {
        if (!$this->rendezVous->contains($rendezVous)) {
            $this->rendezVous[] = $rendezVous;
            $rendezVous->setPatient($this);
        }

        return $this;
    }

    public function removeRendezVou(RendezVous $rendezVous): self
    {
        if ($this->rendezVous->contains($rendezVous)) {
            $this->rendezVous->removeElement($rendezVous);
            // set the owning side to null (unless already changed)
            if ($rendezVous->getPatient() === $this) {
                $rendezVous->setPatient(null);
            }
        }

        return $this;
    }

    public function getSoignantReferent(): ?string
    {
        return $this->soignantReferent;
    }

    public function setSoignantReferent(?string $soignantReferent): self
    {
        $this->soignantReferent = $soignantReferent;

        return $this;
    }

    public function getMode(): ?string
    {
        return $this->mode;
    }

    public function setMode(?string $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getOffre(): ?string
    {
        return $this->offre;
    }

    public function setOffre(?string $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDiagnosticEducatif(): ?DiagnosticEducatif
    {
        return $this->diagnosticEducatif;
    }

    public function setDiagnosticEducatif(?DiagnosticEducatif $diagnosticEducatif): self
    {
        $this->diagnosticEducatif = $diagnosticEducatif;

        return $this;
    }
}
