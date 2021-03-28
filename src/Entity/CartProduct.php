<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartProductRepository::class)
 * @ORM\Table(name="cart_product")
 */
class CartProduct
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
     * @ORM\ManyToOne(targetEntity=Cart::class, inversedBy="cartProducts")
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private Cart $cart;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    private Product $product;

    public function __construct(int $quantity, Cart $cart, Product $product)
    {
        $this->quantity = $quantity;
        $this->cart = $cart;
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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProductId(): string
    {
        return $this->getProduct()->getId();
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

    public function getCartProductPrice(): int
    {
        return $this->quantity*$this->getProductPrice();
    }
}
