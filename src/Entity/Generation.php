<?php

namespace App\Entity;

use App\Repository\GenerationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GenerationRepository::class)
 *
 * @ApiResource(
 *       normalizationContext={"groups"={"generation"}},
 *       paginationEnabled=false
 * )
 */
class Generation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"pokemon", "type", "generation"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"pokemon", "type", "generation"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Pokemon::class, mappedBy="generation")
     */
    private $pokemon;

    public function __construct()
    {
        $this->pokemon = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Pokemon[]
     */
    public function getPokemon(): Collection
    {
        return $this->pokemon;
    }

    public function addPokemon(Pokemon $pokemon): self
    {
        if (!$this->pokemon->contains($pokemon)) {
            $this->pokemon[] = $pokemon;
            $pokemon->setGeneration($this);
        }

        return $this;
    }

    public function removePokemon(Pokemon $pokemon): self
    {
        if ($this->pokemon->removeElement($pokemon)) {
            // set the owning side to null (unless already changed)
            if ($pokemon->getGeneration() === $this) {
                $pokemon->setGeneration(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
