<?php

declare(strict_types=1);

namespace App\DomainObject\ValueObject;

use InvalidArgumentException;

class ProductVO
{
    private string $name;
    private int $price;
    private int $stock;

    public function __construct(string $name, int $price, int $stock)
    {
        $this->validateNameNotEmpty($name);
        $this->validateProductPriceBiggerThanZero($price);
        $this->validateProductStockNotNegative($stock);
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    private function validateNameNotEmpty(string $name): void
    {
        if ('' === $name) {
            throw new InvalidArgumentException('Product Name can not be empty.');
        }
    }

    /**
     * @param int $price
     */
    private function validateProductPriceBiggerThanZero(int $price): void
    {
        if (0 >= $price) {
            throw new InvalidArgumentException("Product price must be a positive number (received: $price).");
        }
    }

    /**
     * @param int $stock
     */
    private function validateProductStockNotNegative(int $stock): void
    {
        if (0 > $stock) {
            throw new InvalidArgumentException("Stock must be a positive number (received: $stock).");
        }
    }

}
