<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartProductRepository::class)
 * @ORM\Table(name="cart_product")
 */
class CartProductDE
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36)
     */
    private string $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=ProductDE::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private ProductDE $product;

    public function __construct(int $quantity, ProductDE $product) {
        $this->quantity = $quantity;
        $this->product = $product;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getProduct(): ?ProductDE
    {
        return $this->product;
    }

    public function setProduct(?ProductDE $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProductName(): string
    {
        return $this->getProduct()->getName();
    }

    public function getProductPrice(): int
    {
        return $this->getProduct()->getPrice();
    }

    public function getProductStock(): int
    {
        return $this->getProduct()->getStock();
    }
}
