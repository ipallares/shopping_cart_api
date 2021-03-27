<?php

declare(strict_types=1);

namespace App\Logic\Converter;

use App\DomainObject\Entity\ProductEntity;
use App\Entity\ProductDE;

class ProductDeToEntity
{
    public function convert(ProductDE $product): ProductEntity
    {
        return new ProductEntity(
            $product->getId(),
            $product->getName(),
            $product->getPrice(),
            $product->getStock()
        );
    }
}
