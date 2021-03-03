<?php

namespace App\Entity;

use App\Repository\SlotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SlotRepository::class)
 */
class Slot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"slot"})
     */
    private $id;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureDebut;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureFin;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $categorie;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thematique;

    /**
     * @Groups({"slot"})
     * @ORM\ManyToOne(targetEntity=Soignant::class, inversedBy="slots")
     */
    private $soignant;

    /**
     * @ORM\ManyToOne(targetEntity=Semaine::class, inversedBy="slots", cascade={"persist"})
     */
    private $semaine;

    /**
     * @Groups({"slot"})
     * @ORM\OneToMany(targetEntity=RendezVous::class, mappedBy="slot", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $rendezVous;

    /**
     * @Groups({"slot"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $place;

    public function __construct()
    {
        $this->rendezVous = new ArrayCollection();
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

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $heureDebut): self
    {
        $this->heureDebut = $heureDebut;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $heureFin): self
    {
        $this->heureFin = $heureFin;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

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

    public function getThematique(): ?string
    {
        return $this->thematique;
    }

    public function setThematique(?string $thematique): self
    {
        $this->thematique = $thematique;

        return $this;
    }

    public function getSoignant(): ?Soignant
    {
        return $this->soignant;
    }

    public function setSoignant(?Soignant $soignant): self
    {
        $this->soignant = $soignant;

        return $this;
    }

    public function getSemaine(): ?Semaine
    {
        return $this->semaine;
    }

    public function setSemaine(?Semaine $semaine): self
    {
        $this->semaine = $semaine;

        return $this;
    }

    /**
     * @return Collection|RendezVous[]
     */
    public function getRendezVous(): Collection
    {
        return $this->rendezVous;
    }

    public function addRendezVous(RendezVous $rendezVous): self
    {
        if (!$this->rendezVous->contains($rendezVous)) {
            $this->rendezVous[] = $rendezVous;
            $rendezVous->setSlot($this);
        }

        return $this;
    }

    public function removeRendezVous(RendezVous $rendezVous): self
    {
        if ($this->rendezVous->removeElement($rendezVous)) {
            // set the owning side to null (unless already changed)
            if ($rendezVous->getSlot() === $this) {
                $rendezVous->setSlot(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return strval($this->id);
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(?int $place): self
    {
        $this->place = $place;

        return $this;
    }
}
