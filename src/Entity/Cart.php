<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CartRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 * @ORM\Table(name="cart")
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="string", length=36)
     */
    private string $id;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $creationDate;

    /**
     * @ORM\Column(type="date")
     */
    private DateTimeInterface $lastModified;

    /**
     * @ORM\OneToMany(targetEntity=CartProduct::class, mappedBy="cart", orphanRemoval=true, cascade={"persist"})
     * @var Collection<int, CartProduct>
     */
    private Collection $cartProducts;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
        $this->creationDate = new DateTime();
        $this->lastModified = new DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getLastModified(): ?DateTimeInterface
    {
        return $this->lastModified;
    }

    public function setLastModified(DateTimeInterface $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * @return Collection|CartProduct[]
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    /**
     * @param Collection $cartProducts
     *
     * @return $this
     */
    public function setCartProducts(Collection $cartProducts): self
    {
        $this->cartProducts = $cartProducts;

        return $this;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts[] = $cartProduct;
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        $this->cartProducts->removeElement($cartProduct);

        return $this;
    }
}
