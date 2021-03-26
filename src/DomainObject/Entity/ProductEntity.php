<?php

declare(strict_types=1);

namespace App\DomainObject\Entity;

use App\DomainObject\ValueObject\ProductVO;
use App\DomainObject\ValueObject\UuidVO;

class ProductEntity extends ProductVO
{
    private UuidVO $id;

    public function __construct(string $id, string $name, int $price, int $stock)
    {
        $this->id = new UuidVO($id);
        parent::__construct($name, $price, $stock);
    }

    public function getId(): string
    {
        return $this->id->getId();
    }
}
