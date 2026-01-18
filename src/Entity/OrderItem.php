<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // RELATION VERS LA COMMANDE (Beaucoup d'items pour une commande)
    #[ORM\ManyToOne(inversedBy: 'orderItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $order = null;

    // RELATION VERS LE PRODUIT (Beaucoup d'items peuvent pointer vers le même produit)
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column] // Changé en int pour calculs, ou string si tu préfères
    private ?int $quantity = null;

    #[ORM\Column]
    private ?float $unit_price = null; // Changé en float pour les prix avec virgule

    public function getId(): ?int
    {
        return $this->id;
    }

    // On utilise l'objet Order, pas l'ID
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;
        return $this;
    }

    // On utilise l'objet Product, pas l'ID
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return (int)$this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unit_price;
    }

    public function setUnitPrice(float $unit_price): static
    {
        $this->unit_price = $unit_price;
        return $this;
    }
}