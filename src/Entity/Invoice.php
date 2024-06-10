<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank(message: "La date d'émission ne peut pas être vide.")]
    #[Assert\LessThanOrEqual("today", message: "La date d'émission ne peut pas être dans le futur.")]
    private ?\DateTimeInterface $issuedAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "issuedAt",
        message: "La date de paiement doit être postérieure ou égale à la date d'émission."
    )]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\OneToOne(inversedBy: 'invoice')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "La réservation associée ne peut pas être vide.")]
    private ?Booking $booking = null;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le montant ne peut pas être vide.")]
    #[Assert\Positive(message: "Le montant doit être supérieur à zéro.")]
    private ?float $amount = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: "L'identifiant de session Stripe ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $stripeSessionId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIssuedAt(): ?\DateTimeInterface
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeInterface $issuedAt): self
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(?Booking $booking): self
    {
        $this->booking = $booking;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(?string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;
        return $this;
    }
}


