<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 */
class Grade
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"default"})
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $ECUE;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $project;

    /**
     * @ORM\Column(type="float")
     * @Groups({"default"})
     */
    private $value;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"default"})
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Student", inversedBy="grades")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

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

    public function getECUE(): ?string
    {
        return $this->ECUE;
    }

    public function setECUE(string $ECUE): self
    {
        $this->ECUE = $ECUE;

        return $this;
    }

    public function getProject(): ?string
    {
        return $this->project;
    }

    public function setProject(string $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getOwner(): ?Student
    {
        return $this->owner;
    }

    public function setOwner(?Student $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
