<?php

declare(strict_types=1);

namespace App\Logic\Converter;

use App\DomainObject\Entity\ProductEntity;
use App\Entity\ProductDE;

class ProductEntityToDE
{
    public function convert(ProductEntity $product): ProductDE
    {
        $productDE = new ProductDE($product->getName(), $product->getPrice(), $product->getStock());
        $productDE->setId($product->getId());

        return $productDE;
    }
}
