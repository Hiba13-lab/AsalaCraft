<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?float $total = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $shipping_address = null;

    #[ORM\Column(length: 255)]
    private ?string $payment_method = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    // AJOUT DE LA RELATION VERS ORDERITEM
    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, orphanRemoval: true)]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getTotal(): ?float { return $this->total; }
    public function setTotal(float $total): static { $this->total = $total; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    /** @return Collection<int, OrderItem> */
    public function getOrderItems(): Collection { return $this->orderItems; }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
        }
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->created_at; }
    public function setCreatedAt(\DateTimeImmutable $created_at): static { $this->created_at = $created_at; return $this; }

    // On garde tes autres getters/setters (ShippingAddress, PaymentMethod...)
    public function getShippingAddress(): ?string { return $this->shipping_address; }
    public function setShippingAddress(string $shipping_address): static { $this->shipping_address = $shipping_address; return $this; }
    public function getPaymentMethod(): ?string { return $this->payment_method; }
    public function setPaymentMethod(string $payment_method): static { $this->payment_method = $payment_method; return $this; }
}