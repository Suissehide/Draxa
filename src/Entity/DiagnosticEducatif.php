<?php

namespace App\Entity;

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
     * @ORM\Column(type="array", nullable=true)
     */
    private $facteursRisque;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $qualiteVie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $qualiteVieLibre;

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
    private $connaissancesMaladieLibre;

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

    /**
     * @ORM\Column(type="array", length=255, nullable=true)
     */
    private $ometFDR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gestionTensionArterielle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionTensionArterielleLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionTensionArterielleSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionTensionArterielleEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gestionHba1c;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionHba1cLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionHba1cSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionHba1cEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gestionLDL;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionLDLLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionLDLSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionLDLEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adhesionTraitement;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $adhesionTraitementLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $adhesionTraitementSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $adhesionTraitementEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alimentation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $alimentationLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $alimentationSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $alimentationEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gestionStress;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionStressLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionStressSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionStressEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $consommationTabac;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $consommationTabacNombreCigaretteJour;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $consommationTabacLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $consommationTabacSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $consommationTabacEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gestionTourTaille;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionTourTailleLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionTourTailleSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $gestionTourTailleEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activitePhysique;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $activitePhysiqueLibre;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $activitePhysiqueSentimentAutoEfficacite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $activitePhysiqueEtapeChangement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gestionFDR;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $impactSurQualiteVie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stadeAcceptationMaladie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $soutienSocial;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $projetDeVie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $objectifsPatient;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $objectifsSoignants;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $suiviEducatifNegocie;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $rapatrier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFacteursRisque(): ?array
    {
        return $this->facteursRisque;
    }

    public function setFacteursRisque(?array $facteursRisque): self
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

    public function getQualiteVieLibre(): ?string
    {
        return $this->qualiteVieLibre;
    }

    public function setQualiteVieLibre(?string $qualiteVieLibre): self
    {
        $this->qualiteVieLibre = $qualiteVieLibre;

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

    public function getConnaissancesMaladieLibre(): ?string
    {
        return $this->connaissancesMaladieLibre;
    }

    public function setConnaissancesMaladieLibre(?string $connaissancesMaladieLibre): self
    {
        $this->connaissancesMaladieLibre = $connaissancesMaladieLibre;

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

    public function getOmetFDR(): ?array
    {
        return $this->ometFDR;
    }

    public function setOmetFDR(?array $ometFDR): self
    {
        $this->ometFDR = $ometFDR;

        return $this;
    }

    public function getGestionTensionArterielle(): ?string
    {
        return $this->gestionTensionArterielle;
    }

    public function setGestionTensionArterielle(?string $gestionTensionArterielle): self
    {
        $this->gestionTensionArterielle = $gestionTensionArterielle;

        return $this;
    }

    public function getGestionTensionArterielleLibre(): ?string
    {
        return $this->gestionTensionArterielleLibre;
    }

    public function setGestionTensionArterielleLibre(?string $gestionTensionArterielleLibre): self
    {
        $this->gestionTensionArterielleLibre = $gestionTensionArterielleLibre;

        return $this;
    }

    public function getGestionTensionArterielleSentimentAutoEfficacite(): ?string
    {
        return $this->gestionTensionArterielleSentimentAutoEfficacite;
    }

    public function setGestionTensionArterielleSentimentAutoEfficacite(?string $gestionTensionArterielleSentimentAutoEfficacite): self
    {
        $this->gestionTensionArterielleSentimentAutoEfficacite = $gestionTensionArterielleSentimentAutoEfficacite;

        return $this;
    }

    public function getGestionTensionArterielleEtapeChangement(): ?string
    {
        return $this->gestionTensionArterielleEtapeChangement;
    }

    public function setGestionTensionArterielleEtapeChangement(?string $gestionTensionArterielleEtapeChangement): self
    {
        $this->gestionTensionArterielleEtapeChangement = $gestionTensionArterielleEtapeChangement;

        return $this;
    }

    public function getGestionHba1c(): ?string
    {
        return $this->gestionHba1c;
    }

    public function setGestionHba1c(?string $gestionHba1c): self
    {
        $this->gestionHba1c = $gestionHba1c;

        return $this;
    }

    public function getGestionHba1cLibre(): ?string
    {
        return $this->gestionHba1cLibre;
    }

    public function setGestionHba1cLibre(?string $gestionHba1cLibre): self
    {
        $this->gestionHba1cLibre = $gestionHba1cLibre;

        return $this;
    }

    public function getGestionHba1cSentimentAutoEfficacite(): ?string
    {
        return $this->gestionHba1cSentimentAutoEfficacite;
    }

    public function setGestionHba1cSentimentAutoEfficacite(?string $gestionHba1cSentimentAutoEfficacite): self
    {
        $this->gestionHba1cSentimentAutoEfficacite = $gestionHba1cSentimentAutoEfficacite;

        return $this;
    }

    public function getGestionHba1cEtapeChangement(): ?string
    {
        return $this->gestionHba1cEtapeChangement;
    }

    public function setGestionHba1cEtapeChangement(?string $gestionHba1cEtapeChangement): self
    {
        $this->gestionHba1cEtapeChangement = $gestionHba1cEtapeChangement;

        return $this;
    }

    public function getGestionLDL(): ?string
    {
        return $this->gestionLDL;
    }

    public function setGestionLDL(?string $gestionLDL): self
    {
        $this->gestionLDL = $gestionLDL;

        return $this;
    }

    public function getGestionLDLLibre(): ?string
    {
        return $this->gestionLDLLibre;
    }

    public function setGestionLDLLibre(?string $gestionLDLLibre): self
    {
        $this->gestionLDLLibre = $gestionLDLLibre;

        return $this;
    }

    public function getGestionLDLSentimentAutoEfficacite(): ?string
    {
        return $this->gestionLDLSentimentAutoEfficacite;
    }

    public function setGestionLDLSentimentAutoEfficacite(?string $gestionLDLSentimentAutoEfficacite): self
    {
        $this->gestionLDLSentimentAutoEfficacite = $gestionLDLSentimentAutoEfficacite;

        return $this;
    }

    public function getGestionLDLEtapeChangement(): ?string
    {
        return $this->gestionLDLEtapeChangement;
    }

    public function setGestionLDLEtapeChangement(?string $gestionLDLEtapeChangement): self
    {
        $this->gestionLDLEtapeChangement = $gestionLDLEtapeChangement;

        return $this;
    }

    public function getAdhesionTraitement(): ?string
    {
        return $this->adhesionTraitement;
    }

    public function setAdhesionTraitement(?string $adhesionTraitement): self
    {
        $this->adhesionTraitement = $adhesionTraitement;

        return $this;
    }

    public function getAdhesionTraitementLibre(): ?string
    {
        return $this->adhesionTraitementLibre;
    }

    public function setAdhesionTraitementLibre(?string $adhesionTraitementLibre): self
    {
        $this->adhesionTraitementLibre = $adhesionTraitementLibre;

        return $this;
    }

    public function getAdhesionTraitementSentimentAutoEfficacite(): ?string
    {
        return $this->adhesionTraitementSentimentAutoEfficacite;
    }

    public function setAdhesionTraitementSentimentAutoEfficacite(?string $adhesionTraitementSentimentAutoEfficacite): self
    {
        $this->adhesionTraitementSentimentAutoEfficacite = $adhesionTraitementSentimentAutoEfficacite;

        return $this;
    }

    public function getAdhesionTraitementEtapeChangement(): ?string
    {
        return $this->adhesionTraitementEtapeChangement;
    }

    public function setAdhesionTraitementEtapeChangement(?string $adhesionTraitementEtapeChangement): self
    {
        $this->adhesionTraitementEtapeChangement = $adhesionTraitementEtapeChangement;

        return $this;
    }

    public function getAlimentation(): ?string
    {
        return $this->alimentation;
    }

    public function setAlimentation(?string $alimentation): self
    {
        $this->alimentation = $alimentation;

        return $this;
    }

    public function getAlimentationLibre(): ?string
    {
        return $this->alimentationLibre;
    }

    public function setAlimentationLibre(?string $alimentationLibre): self
    {
        $this->alimentationLibre = $alimentationLibre;

        return $this;
    }

    public function getAlimentationSentimentAutoEfficacite(): ?string
    {
        return $this->alimentationSentimentAutoEfficacite;
    }

    public function setAlimentationSentimentAutoEfficacite(?string $alimentationSentimentAutoEfficacite): self
    {
        $this->alimentationSentimentAutoEfficacite = $alimentationSentimentAutoEfficacite;

        return $this;
    }

    public function getAlimentationEtapeChangement(): ?string
    {
        return $this->alimentationEtapeChangement;
    }

    public function setAlimentationEtapeChangement(?string $alimentationEtapeChangement): self
    {
        $this->alimentationEtapeChangement = $alimentationEtapeChangement;

        return $this;
    }

    public function getGestionStress(): ?string
    {
        return $this->gestionStress;
    }

    public function setGestionStress(?string $gestionStress): self
    {
        $this->gestionStress = $gestionStress;

        return $this;
    }

    public function getGestionStressLibre(): ?string
    {
        return $this->gestionStressLibre;
    }

    public function setGestionStressLibre(?string $gestionStressLibre): self
    {
        $this->gestionStressLibre = $gestionStressLibre;

        return $this;
    }

    public function getGestionStressSentimentAutoEfficacite(): ?string
    {
        return $this->gestionStressSentimentAutoEfficacite;
    }

    public function setGestionStressSentimentAutoEfficacite(?string $gestionStressSentimentAutoEfficacite): self
    {
        $this->gestionStressSentimentAutoEfficacite = $gestionStressSentimentAutoEfficacite;

        return $this;
    }

    public function getGestionStressEtapeChangement(): ?string
    {
        return $this->gestionStressEtapeChangement;
    }

    public function setGestionStressEtapeChangement(?string $gestionStressEtapeChangement): self
    {
        $this->gestionStressEtapeChangement = $gestionStressEtapeChangement;

        return $this;
    }

    public function getConsommationTabac(): ?string
    {
        return $this->consommationTabac;
    }

    public function setConsommationTabac(?string $consommationTabac): self
    {
        $this->consommationTabac = $consommationTabac;

        return $this;
    }

    public function getConsommationTabacNombreCigaretteJour(): ?int
    {
        return $this->consommationTabacNombreCigaretteJour;
    }

    public function setConsommationTabacNombreCigaretteJour(?int $consommationTabacNombreCigaretteJour): self
    {
        $this->consommationTabacNombreCigaretteJour = $consommationTabacNombreCigaretteJour;

        return $this;
    }

    public function getConsommationTabacLibre(): ?string
    {
        return $this->consommationTabacLibre;
    }

    public function setConsommationTabacLibre(?string $consommationTabacLibre): self
    {
        $this->consommationTabacLibre = $consommationTabacLibre;

        return $this;
    }

    public function getConsommationTabacSentimentAutoEfficacite(): ?string
    {
        return $this->consommationTabacSentimentAutoEfficacite;
    }

    public function setConsommationTabacSentimentAutoEfficacite(?string $consommationTabacSentimentAutoEfficacite): self
    {
        $this->consommationTabacSentimentAutoEfficacite = $consommationTabacSentimentAutoEfficacite;

        return $this;
    }

    public function getConsommationTabacEtapeChangement(): ?string
    {
        return $this->consommationTabacEtapeChangement;
    }

    public function setConsommationTabacEtapeChangement(?string $consommationTabacEtapeChangement): self
    {
        $this->consommationTabacEtapeChangement = $consommationTabacEtapeChangement;

        return $this;
    }

    public function getGestionTourTaille(): ?string
    {
        return $this->gestionTourTaille;
    }

    public function setGestionTourTaille(?string $gestionTourTaille): self
    {
        $this->gestionTourTaille = $gestionTourTaille;

        return $this;
    }

    public function getGestionTourTailleLibre(): ?string
    {
        return $this->gestionTourTailleLibre;
    }

    public function setGestionTourTailleLibre(?string $gestionTourTailleLibre): self
    {
        $this->gestionTourTailleLibre = $gestionTourTailleLibre;

        return $this;
    }

    public function getGestionTourTailleSentimentAutoEfficacite(): ?string
    {
        return $this->gestionTourTailleSentimentAutoEfficacite;
    }

    public function setGestionTourTailleSentimentAutoEfficacite(?string $gestionTourTailleSentimentAutoEfficacite): self
    {
        $this->gestionTourTailleSentimentAutoEfficacite = $gestionTourTailleSentimentAutoEfficacite;

        return $this;
    }

    public function getGestionTourTailleEtapeChangement(): ?string
    {
        return $this->gestionTourTailleEtapeChangement;
    }

    public function setGestionTourTailleEtapeChangement(?string $gestionTourTailleEtapeChangement): self
    {
        $this->gestionTourTailleEtapeChangement = $gestionTourTailleEtapeChangement;

        return $this;
    }

    public function getActivitePhysique(): ?string
    {
        return $this->activitePhysique;
    }

    public function setActivitePhysique(?string $activitePhysique): self
    {
        $this->activitePhysique = $activitePhysique;

        return $this;
    }

    public function getActivitePhysiqueLibre(): ?string
    {
        return $this->activitePhysiqueLibre;
    }

    public function setActivitePhysiqueLibre(?string $activitePhysiqueLibre): self
    {
        $this->activitePhysiqueLibre = $activitePhysiqueLibre;

        return $this;
    }

    public function getActivitePhysiqueSentimentAutoEfficacite(): ?string
    {
        return $this->activitePhysiqueSentimentAutoEfficacite;
    }

    public function setActivitePhysiqueSentimentAutoEfficacite(?string $activitePhysiqueSentimentAutoEfficacite): self
    {
        $this->activitePhysiqueSentimentAutoEfficacite = $activitePhysiqueSentimentAutoEfficacite;

        return $this;
    }

    public function getActivitePhysiqueEtapeChangement(): ?string
    {
        return $this->activitePhysiqueEtapeChangement;
    }

    public function setActivitePhysiqueEtapeChangement(?string $activitePhysiqueEtapeChangement): self
    {
        $this->activitePhysiqueEtapeChangement = $activitePhysiqueEtapeChangement;

        return $this;
    }

    public function getGestionFDR(): ?string
    {
        return $this->gestionFDR;
    }

    public function setGestionFDR(?string $gestionFDR): self
    {
        $this->gestionFDR = $gestionFDR;

        return $this;
    }

    public function getImpactSurQualiteVie(): ?string
    {
        return $this->impactSurQualiteVie;
    }

    public function setImpactSurQualiteVie(?string $impactSurQualiteVie): self
    {
        $this->impactSurQualiteVie = $impactSurQualiteVie;

        return $this;
    }

    public function getStadeAcceptationMaladie(): ?string
    {
        return $this->stadeAcceptationMaladie;
    }

    public function setStadeAcceptationMaladie(?string $stadeAcceptationMaladie): self
    {
        $this->stadeAcceptationMaladie = $stadeAcceptationMaladie;

        return $this;
    }

    public function getSoutienSocial(): ?string
    {
        return $this->soutienSocial;
    }

    public function setSoutienSocial(?string $soutienSocial): self
    {
        $this->soutienSocial = $soutienSocial;

        return $this;
    }

    public function getProjetDeVie(): ?string
    {
        return $this->projetDeVie;
    }

    public function setProjetDeVie(?string $projetDeVie): self
    {
        $this->projetDeVie = $projetDeVie;

        return $this;
    }

    public function getObjectifsPatient(): ?string
    {
        return $this->objectifsPatient;
    }

    public function setObjectifsPatient(?string $objectifsPatient): self
    {
        $this->objectifsPatient = $objectifsPatient;

        return $this;
    }

    public function getObjectifsSoignants(): ?array
    {
        return $this->objectifsSoignants;
    }

    public function setObjectifsSoignants(?array $objectifsSoignants): self
    {
        $this->objectifsSoignants = $objectifsSoignants;

        return $this;
    }

    public function getSuiviEducatifNegocie(): ?string
    {
        return $this->suiviEducatifNegocie;
    }

    public function setSuiviEducatifNegocie(?string $suiviEducatifNegocie): self
    {
        $this->suiviEducatifNegocie = $suiviEducatifNegocie;

        return $this;
    }

    public function getRapatrier(): ?string
    {
        return $this->rapatrier;
    }

    public function setRapatrier(?string $rapatrier): self
    {
        $this->rapatrier = $rapatrier;

        return $this;
    }
}
