<?php

declare(strict_types=1);

namespace App\DomainObject\ValueObject;

use InvalidArgumentException;

class CartProductVO
{
    private int $quantity;
    private string $productName;
    private int $productPrice;
    private int $productStock;
    private UuidVO $productId;

    public function __construct(
        int $quantity,
        string $productName,
        int $productPrice,
        int $productStock,
        string $productId
    ) {
        $this->validateQuantityBiggerThanZero($quantity);
        $this->validateProductStockBiggerThanZero($productStock);
        $this->validateQuantityNotBiggerThanStock($quantity, $productStock);
        $this->validateProductNameNotEmpty($productName);
        $this->validateProductPriceBiggerThanZero($productPrice);

        $this->quantity = $quantity;
        $this->productName = $productName;
        $this->productPrice = $productPrice;
        $this->productStock = $productStock;
        $this->productId = new UuidVO($productId);
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getProductPrice(): float
    {
        return $this->productPrice;
    }

    public function getProductStock(): int
    {
        return $this->productStock;
    }

    public function getCartProductPrice(): float {
        return $this->productPrice * $this->quantity;
    }

    public function getProductId(): string
    {
        return $this->productId->getId();
    }

    /**
     * @param int $quantity
     */
    private function validateQuantityBiggerThanZero(int $quantity): void
    {
        if (0 >= $quantity) {
            throw new InvalidArgumentException("Product quantity must be a positive number (received: $quantity).");
        }
    }

    /**
     * @param int $quantity
     * @param int $stock
     */
    private function validateQuantityNotBiggerThanStock(int $quantity, int $stock): void
    {
        if ($quantity > $stock) {
            throw new InvalidArgumentException("Product quantity can't be bigger than current product stock (quantity 
                                                received: $quantity, product stock: $stock).");
        }
    }

    /**
     * @param string $productName
     */
    private function validateProductNameNotEmpty(string $productName): void
    {
        if ('' === $productName) {
            throw new InvalidArgumentException('Product Name can not be empty.');
        }
    }

    /**
     * @param int $productPrice
     */
    private function validateProductPriceBiggerThanZero(int $productPrice): void
    {
        if (0 >= $productPrice) {
            throw new InvalidArgumentException("Product price must be a positive number (received: $productPrice).");
        }
    }

    /**
     * @param int $stock
     */
    private function validateProductStockBiggerThanZero(int $stock): void
    {
        if (0 > $stock) {
            throw new InvalidArgumentException("Product stock must be a bigger than 0 (received: $stock).");
        }
    }
}
