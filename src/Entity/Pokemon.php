<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PokemonRepository::class)
 *
 * @ApiResource(
 *       normalizationContext={"groups"={"pokemon"}},
 *       paginationEnabled=false
 * )
 * @Vich\Uploadable
 */
class Pokemon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"pokemon", "generation", "type"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon", "generation", "type"})
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"pokemon", "generation", "type"})
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon"})
     */
    private $vie;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon"})
     */
    private $attaque;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"pokemon"})
     */
    private $defense;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"pokemon"})
     */
    private $legendaire;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="pokemon")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"pokemon"})
     */
    private $type1;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="pokemon2")
     * @Groups({"pokemon"})
     */
    private $type2;

    /**
     * @ORM\ManyToOne(targetEntity=Generation::class, inversedBy="pokemon")
     * @Groups({"pokemon"})
     */
    private $generation;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * 
     * @Vich\UploadableField(mapping="product_image", fileNameProperty="imageName", size="imageSize")
     * 
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int|null
     */
    private $imageSize;

    public function __construct()
    {
        $this->image = new EmbeddedFile();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

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

    public function getVie(): ?int
    {
        return $this->vie;
    }

    public function setVie(int $vie): self
    {
        $this->vie = $vie;

        return $this;
    }

    public function getAttaque(): ?int
    {
        return $this->attaque;
    }

    public function setAttaque(int $attaque): self
    {
        $this->attaque = $attaque;

        return $this;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getLegendaire(): ?bool
    {
        return $this->legendaire;
    }

    public function setLegendaire(bool $legendaire): self
    {
        $this->legendaire = $legendaire;

        return $this;
    }

    public function getType1(): ?Type
    {
        return $this->type1;
    }

    public function setType1(?Type $type1): self
    {
        $this->type1 = $type1;

        return $this;
    }

    public function getType2(): ?Type
    {
        return $this->type2;
    }

    public function setType2(?Type $type2): self
    {
        $this->type2 = $type2;

        return $this;
    }

    public function getGeneration(): ?Generation
    {
        return $this->generation;
    }

    public function setGeneration(?Generation $generation): self
    {
        $this->generation = $generation;

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    
    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }
}
