<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[Vich\Uploadable]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // NOTE: This is not a mapped field of entity metadata, just a simple property.
    #[Vich\UploadableField(mapping: 'gallery', fileNameProperty: 'imageName')]
    #[Assert\File(
        maxSize: '5M',
        maxSizeMessage: "La taille de l'image ne peut pas dépasser {{ limit }}.",
        mimeTypes: ["image/jpeg", "image/png", "image/gif"],
        mimeTypesMessage: "Veuillez télécharger une image valide (jpeg, png, gif)."
    )]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le texte alternatif ne peut pas être vide.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le texte alternatif ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $altText = null;

    #[ORM\ManyToOne(inversedBy: 'galleries')]
    #[Assert\NotBlank(message: "Le produit associé ne peut pas être vide.")]
    private ?Product $idBootcamps = null;

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
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // Il est nécessaire qu'au moins un champ change si vous utilisez doctrine
            // sinon les listeners d'événements ne seront pas appelés et le fichier est perdu
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
    
    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function setAltText(?string $altText): void
    {
        $this->altText = $altText;
    }

    public function getIdBootcamps(): ?Product
    {
        return $this->idBootcamps;
    }

    public function setIdBootcamps(?Product $idBootcamps): static
    {
        $this->idBootcamps = $idBootcamps;

        return $this;
    }
}
