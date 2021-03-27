<?php

declare(strict_types=1);

namespace App\Logic\Converter;

use App\DomainObject\Entity\CartProductEntity;
use App\Entity\CartProductDE;

class CartProductDeToEntity
{
    public function convert(CartProductDE $cartProduct)
    {
        return new CartProductEntity(
            $cartProduct->getId(),
            $cartProduct->getQuantity(),
            $cartProduct->getProductName(),
            $cartProduct->getProductPrice(),
            $cartProduct->getProductStock(),
            $cartProduct->getProduct()->getId()
        );
    }
}
