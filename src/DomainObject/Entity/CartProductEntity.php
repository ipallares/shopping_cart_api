<?php

declare(strict_types=1);

namespace App\DomainObject\Entity;

use App\DomainObject\ValueObject\CartProductVO;
use App\DomainObject\ValueObject\UuidVO;

class CartProductEntity extends CartProductVO
{
    private UuidVO $id;

    public function __construct(
        string $id,
        int $quantity,
        string $productName,
        int $productPrice,
        int $productStock,
        string $productId
    ) {
        $this->id = new UuidVO($id);
        parent::__construct($quantity, $productName, $productPrice, $productStock, $productId);
    }

    public function getId(): string
    {
        return $this->id->getId();
    }
}
