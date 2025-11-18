<?php

namespace App\Entity;

use App\Repository\HamsterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: HamsterRepository::class)]
class Hamster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le champ ne peut pas être vide.")]
    #[Assert\Length(
        min: 2,
        minMessage: "Le champ doit contenir au moins {{ limit }} caractères."
    )]
    #[Groups(['user_details'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le champ ne peut pas être vide.")]
    #[Assert\Range(
        min: 0,
        max: 100,
        notInRangeMessage: "La valeur de faim doit être comprise entre {{ min }} et {{ max }}."
    )]
    private ?int $hunger = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le champ ne peut pas être vide.")]
    #[Assert\Range(
        min: 0,
        max: 500,
        notInRangeMessage: "L'âge doit être compris entre {{ min }} et {{ max }}."
    )]
    private ?int $age = null;

    #[ORM\Column(length: 1)]
    #[Assert\NotBlank(message: "Le genre ne peut pas être vide.")]
    private ?string $genre = null;

    #[ORM\Column]
    private ?bool $active = null;

    #[ORM\ManyToOne(inversedBy: 'hamsters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getHunger(): ?int
    {
        return $this->hunger;
    }

    public function setHunger(int $hunger): static
    {
        $this->hunger = $hunger;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): static
    {
        $this->genre = $genre;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
