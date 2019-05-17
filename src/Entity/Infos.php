<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InfosRepository")
 */
class Infos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"patient"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"patient"})
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Patient", inversedBy="infos")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"infos"})
     */
    private $patient;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parametrage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setType(?string $type): self
    {
        $this->type = $type;

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

    public function getMoment(): ?string
    {
        return $this->moment;
    }

    public function setMoment(?string $moment): self
    {
        $this->moment = $moment;

        return $this;
    }

    public function getParametrage(): ?string
    {
        return $this->parametrage;
    }

    public function setParametrage(?string $parametrage): self
    {
        $this->parametrage = $parametrage;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
