<?php

namespace App\Entity;

use App\Repository\DiagnosticEducatifRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DiagnosticEducatifRepository::class)
 */
class DiagnosticEducatif
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facteursRisque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qualiteVie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $viePersonnelle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $vieProfessionnelle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $occupations;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $loisirs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $implication;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $implicationLibre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prioriteSante;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $prioriteSanteLibre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $connaissancesMaladie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $mecanismes;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $localisations;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $symptomes;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $chronicite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reagirSignesAlerte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identificationFDR;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $identificationFDRLibre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacteursRisque(): ?string
    {
        return $this->facteursRisque;
    }

    public function setFacteursRisque(?string $facteursRisque): self
    {
        $this->facteursRisque = $facteursRisque;

        return $this;
    }

    public function getQualiteVie(): ?string
    {
        return $this->qualiteVie;
    }

    public function setQualiteVie(?string $qualiteVie): self
    {
        $this->qualiteVie = $qualiteVie;

        return $this;
    }

    public function getViePersonnelle(): ?string
    {
        return $this->viePersonnelle;
    }

    public function setViePersonnelle(?string $viePersonnelle): self
    {
        $this->viePersonnelle = $viePersonnelle;

        return $this;
    }

    public function getVieProfessionnelle(): ?string
    {
        return $this->vieProfessionnelle;
    }

    public function setVieProfessionnelle(?string $vieProfessionnelle): self
    {
        $this->vieProfessionnelle = $vieProfessionnelle;

        return $this;
    }

    public function getOccupations(): ?string
    {
        return $this->occupations;
    }

    public function setOccupations(?string $occupations): self
    {
        $this->occupations = $occupations;

        return $this;
    }

    public function getLoisirs(): ?string
    {
        return $this->loisirs;
    }

    public function setLoisirs(?string $loisirs): self
    {
        $this->loisirs = $loisirs;

        return $this;
    }

    public function getImplication(): ?string
    {
        return $this->implication;
    }

    public function setImplication(?string $implication): self
    {
        $this->implication = $implication;

        return $this;
    }

    public function getImplicationLibre(): ?string
    {
        return $this->implicationLibre;
    }

    public function setImplicationLibre(?string $implicationLibre): self
    {
        $this->implicationLibre = $implicationLibre;

        return $this;
    }

    public function getPrioriteSante(): ?string
    {
        return $this->prioriteSante;
    }

    public function setPrioriteSante(?string $prioriteSante): self
    {
        $this->prioriteSante = $prioriteSante;

        return $this;
    }

    public function getPrioriteSanteLibre(): ?string
    {
        return $this->prioriteSanteLibre;
    }

    public function setPrioriteSanteLibre(?string $prioriteSanteLibre): self
    {
        $this->prioriteSanteLibre = $prioriteSanteLibre;

        return $this;
    }

    public function getConnaissancesMaladie(): ?string
    {
        return $this->connaissancesMaladie;
    }

    public function setConnaissancesMaladie(?string $connaissancesMaladie): self
    {
        $this->connaissancesMaladie = $connaissancesMaladie;

        return $this;
    }

    public function getMecanismes(): ?string
    {
        return $this->mecanismes;
    }

    public function setMecanismes(?string $mecanismes): self
    {
        $this->mecanismes = $mecanismes;

        return $this;
    }

    public function getLocalisations(): ?string
    {
        return $this->localisations;
    }

    public function setLocalisations(?string $localisations): self
    {
        $this->localisations = $localisations;

        return $this;
    }

    public function getSymptomes(): ?string
    {
        return $this->symptomes;
    }

    public function setSymptomes(?string $symptomes): self
    {
        $this->symptomes = $symptomes;

        return $this;
    }

    public function getChronicite(): ?string
    {
        return $this->chronicite;
    }

    public function setChronicite(?string $chronicite): self
    {
        $this->chronicite = $chronicite;

        return $this;
    }

    public function getReagirSignesAlerte(): ?string
    {
        return $this->reagirSignesAlerte;
    }

    public function setReagirSignesAlerte(?string $reagirSignesAlerte): self
    {
        $this->reagirSignesAlerte = $reagirSignesAlerte;

        return $this;
    }

    public function getIdentificationFDR(): ?string
    {
        return $this->identificationFDR;
    }

    public function setIdentificationFDR(?string $identificationFDR): self
    {
        $this->identificationFDR = $identificationFDR;

        return $this;
    }

    public function getIdentificationFDRLibre(): ?string
    {
        return $this->identificationFDRLibre;
    }

    public function setIdentificationFDRLibre(?string $identificationFDRLibre): self
    {
        $this->identificationFDRLibre = $identificationFDRLibre;

        return $this;
    }
}
