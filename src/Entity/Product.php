<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[Vich\Uploadable]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 100)]
    private ?string $forfait = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $duration = null;

    // Nouveau champ pour le fichier de l'image de fond
    #[Vich\UploadableField(mapping: 'products', fileNameProperty: 'bgName')]
    private ?File $bgFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $bgName = null;

    /**
     * @var Collection<int, Gallery>
     */
    #[ORM\OneToMany(targetEntity: Gallery::class, mappedBy: 'idBootcamps')]
    private Collection $galleries;

    #[ORM\Column]
    private ?float $tarifBase = null;

    #[ORM\Column(length: 50)]
    private ?string $level = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'product')]
    private Collection $bookings;

    public function __construct()
    {
        $this->galleries = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }

    public const DURATION_HALF_DAY = 4.0; // 1/2 journée en heures
    public const DURATION_FULL_DAY = 8.0; // 1 journée en heures
    public const DURATION_TWO_DAYS = 16.0; // 2 jours en heures

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(float $duration): static
    {
        if (!in_array($duration, [self::DURATION_HALF_DAY, self::DURATION_FULL_DAY, self::DURATION_TWO_DAYS])) {
            throw new \InvalidArgumentException("Invalid duration");
        }
        $this->duration = $duration;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function setBgFile(?File $bgFile = null): void
    {
        $this->bgFile = $bgFile;

        if (null !== $bgFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getBgFile(): ?File
    {
        return $this->bgFile;
    }

    public function setBgName(?string $bgName): void
    {
        $this->bgName = $bgName;
    }

    public function getBgName(): ?string
    {
        return $this->bgName;
    }

    public function getForfait(): ?string
    {
        return $this->forfait;
    }

    public function setForfait(string $forfait): static
    {
        $this->forfait = $forfait;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Gallery>
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(Gallery $gallery): static
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries->add($gallery);
            $gallery->setIdBootcamps($this);
        }

        return $this;
    }

    public function removeGallery(Gallery $gallery): static
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getIdBootcamps() === $this) {
                $gallery->setIdBootcamps(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->forfait;
    }

    public function getTarifBase(): ?float
    {
        return $this->tarifBase;
    }

    public function setTarifBase(float $tarifBase): static
    {
        $this->tarifBase = $tarifBase;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setProduct($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getProduct() === $this) {
                $booking->setProduct(null);
            }
        }

        return $this;
    }
}
