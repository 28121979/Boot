<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
#[Vich\Uploadable]
class Profile
{
    public const DEFAULT_AVATAR = 'default-avatar.png';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'avatars', fileNameProperty: 'avatarName')]
    #[Assert\File(
        maxSize: '5M',
        maxSizeMessage: "La taille de l'avatar ne peut pas dépasser {{ limit }}.",
        mimeTypes: ["image/jpeg", "image/png", "image/gif"],
        mimeTypesMessage: "Veuillez télécharger une image valide (jpeg, png, gif)."
    )]
    private ?File $avatarFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $avatarName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le prénom ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $firstname = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "Le champ 'est une entreprise' ne peut pas être vide.")]
    private ?bool $isCompany = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le nom de l'entreprise ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $companyName = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(
        max: 20,
        maxMessage: "Le numéro SIRET ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $siretNumber = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'adresse de facturation ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'adresse de facturation ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $billingAddress = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La ville de facturation ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La ville de facturation ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $billingCity = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le code postal ne peut pas être vide.")]
    #[Assert\Length(
        max: 10,
        maxMessage: "Le code postal ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $zipCode = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: "Le numéro de téléphone ne peut pas être vide.")]
    #[Assert\Length(
        max: 30,
        maxMessage: "Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $phoneNumber = null;

    #[ORM\OneToOne(inversedBy: 'profile', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "L'utilisateur associé ne peut pas être vide.")]
    private ?User $idUser = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'profile')]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Si vous téléchargez un fichier manuellement (c'est-à-dire sans utiliser le formulaire Symfony),
     * assurez-vous qu'une instance de 'UploadedFile' est injectée dans ce setter pour déclencher la mise à jour.
     * Si le paramètre de configuration de ce bundle 'inject_on_load' est défini sur 'true', ce setter
     * doit être capable d'accepter une instance de 'File' car le bundle en injectera une ici
     * pendant l'hydratation de Doctrine.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $avatarFile
     */
    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if (null !== $avatarFile) {
            // Il est nécessaire qu'au moins un champ change si vous utilisez doctrine
            // sinon les listeners d'événements ne seront pas appelés et le fichier est perdu
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarName(?string $avatarName): void
    {
        $this->avatarName = $avatarName;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName ?? self::DEFAULT_AVATAR;
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

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getIsCompany(): ?bool
    {
        return $this->isCompany;
    }

    public function setIsCompany(bool $isCompany): self
    {
        $this->isCompany = $isCompany;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getSiretNumber(): ?string
    {
        return $this->siretNumber;
    }

    public function setSiretNumber(?string $siretNumber): static
    {
        $this->siretNumber = $siretNumber;

        return $this;
    }

    public function getBillingAddress(): ?string
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(string $billingAddress): static
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(string $billingCity): static
    {
        $this->billingCity = $billingCity;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(User $idUser): static
    {
        $this->idUser = $idUser;

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
            $booking->setProfile($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getProfile() === $this) {
                $booking->setProfile(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name . ' ' . $this->firstname;
    }
}
