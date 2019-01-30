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
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
    private $motif;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etp;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sexe;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="date")
     */
    private $dentree;

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
    private $precisions;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $progetp;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $precisionsperso;

    /**
     * @Groups({"patient"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $observ;

    /**
     * @Groups({"patient", "entretiens"})
     * @ORM\OneToMany(targetEntity="App\Entity\Entretien", cascade="all", mappedBy="patient", orphanRemoval=true, indexBy="id")
     */
    private $entretiens;

    /**
     * @Groups({"patient", "annexes"})
     * @ORM\OneToMany(targetEntity="App\Entity\Annexe", cascade="all", mappedBy="patient", orphanRemoval=true, indexBy="id")
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $annexes;

    /**
     * @Groups({"patient", "ateliers"})
     * @ORM\OneToMany(targetEntity="App\Entity\Atelier", cascade="all", mappedBy="patient", orphanRemoval=true, indexBy="id")
     */
    private $ateliers;

    /**
     * @Groups({"patient", "telephoniques"})
     * @ORM\OneToMany(targetEntity="App\Entity\Telephonique", cascade="all", mappedBy="patient", orphanRemoval=true, indexBy="id")
     */
    private $telephoniques;

    /**
     * @Groups({"patient", "rendezVous"})
     * @ORM\OneToMany(targetEntity="App\Entity\RendezVous", cascade="all", mappedBy="patient", orphanRemoval=true, indexBy="id")
     */
    private $rendezVous;

    public function __construct()
    {
        $this->rendezVous = new ArrayCollection();
        $this->entretiens = new ArrayCollection();
        $this->annexes = new ArrayCollection();
        $this->ateliers = new ArrayCollection();
        $this->telephoniques = new ArrayCollection();
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

    public function setDate(\DateTimeInterface $date): self
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

    public function setDentree(\DateTimeInterface $dentree): self
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

    public function setDedate(\DateTimeInterface $dedate): self
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

    /**
     * @return Collection|Entretien[]
     */
    public function getEntretiens(): Collection
    {
        return $this->entretiens;
    }

    public function addEntretien(Entretien $entretiens): self
    {
        if (!$this->entretiens->contains($entretiens)) {
            $this->entretiens[] = $entretiens;
            $entretiens->setPatient($this);
        }

        return $this;
    }

    public function removeEntretien(Entretien $entretiens): self
    {
        if ($this->entretiens->contains($entretiens)) {
            $this->entretiens->removeElement($entretiens);
            // set the owning side to null (unless already changed)
            if ($entretiens->getPatient() === $this) {
                $entretiens->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Annexe[]
     */
    public function getAnnexes(): Collection
    {
        return $this->annexes;
    }

    public function addAnnex(Annexe $annex): self
    {
        if (!$this->annexes->contains($annex)) {
            $this->annexes[] = $annex;
            $annex->setPatient($this);
        }

        return $this;
    }

    public function removeAnnex(Annexe $annex): self
    {
        if ($this->annexes->contains($annex)) {
            $this->annexes->removeElement($annex);
            // set the owning side to null (unless already changed)
            if ($annex->getPatient() === $this) {
                $annex->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Atelier[]
     */
    public function getAteliers(): Collection
    {
        return $this->ateliers;
    }

    public function addAtelier(Atelier $atelier): self
    {
        if (!$this->ateliers->contains($atelier)) {
            $this->ateliers[] = $atelier;
            $atelier->setPatient($this);
        }

        return $this;
    }

    public function removeAtelier(Atelier $atelier): self
    {
        if ($this->ateliers->contains($atelier)) {
            $this->ateliers->removeElement($atelier);
            // set the owning side to null (unless already changed)
            if ($atelier->getPatient() === $this) {
                $atelier->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Telephonique[]
     */
    public function getTelephoniques(): Collection
    {
        return $this->telephoniques;
    }

    public function addTelephonique(Telephonique $telephonique): self
    {
        if (!$this->telephoniques->contains($telephonique)) {
            $this->telephoniques[] = $telephonique;
            $telephonique->setPatient($this);
        }

        return $this;
    }

    public function removeTelephonique(Telephonique $telephonique): self
    {
        if ($this->telephoniques->contains($telephonique)) {
            $this->telephoniques->removeElement($telephonique);
            // set the owning side to null (unless already changed)
            if ($telephonique->getPatient() === $this) {
                $telephonique->setPatient(null);
            }
        }

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
}
