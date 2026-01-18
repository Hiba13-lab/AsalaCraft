<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert; // IMPORT IMPORTANT

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif (supérieur à 0)")]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le stock est obligatoire")]
    #[Assert\PositiveOrZero(message: "Le stock ne peut pas être négatif")]
    private ?int $stock = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SellerProfile $seller = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    // =========================================================
    // NOUVELLE PROPRIÉTÉ : SEUIL D'ALERTE POUR STOCK FAIBLE
    // =========================================================
    #[ORM\Column(type: Types::INTEGER, options: ['default' => 5])]
    #[Assert\PositiveOrZero(message: "Le seuil d'alerte ne peut pas être négatif")]
    private ?int $alertThreshold = 5;

    // ... (Gardez vos getters et setters identiques au-dessous)
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getPrice(): ?float { return $this->price; }
    public function setPrice(float $price): static { $this->price = $price; return $this; }
    public function getStock(): ?int { return $this->stock; }
    public function setStock(int $stock): static { $this->stock = $stock; return $this; }
    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): static { $this->image = $image; return $this; }
    public function getSeller(): ?SellerProfile { return $this->seller; }
    public function setSeller(?SellerProfile $seller): static { $this->seller = $seller; return $this; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): static { $this->category = $category; return $this; }
    public function getSlug(): ?string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }

    // =========================================================
    // GETTERS & SETTERS POUR LE SEUIL D'ALERTE
    // =========================================================
    
    public function getAlertThreshold(): ?int
    {
        return $this->alertThreshold;
    }

    public function setAlertThreshold(int $alertThreshold): static
    {
        $this->alertThreshold = $alertThreshold;

        return $this;
    }
}