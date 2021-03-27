<?php

declare(strict_types=1);

namespace App\Tests\DomainObject\Entity;

use App\DomainObject\Entity\ProductEntity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductEntityTest extends TestCase
{
    public function testValidData(): void
    {
        $id = uuid_create();
        $productName = 'productName';
        $productPrice = 1199;
        $productStock= 0;

        $productVO = new ProductEntity(
            $id,
            $productName,
            $productPrice,
            $productStock
        );

        $this->assertEquals($id, $productVO->getId());
        $this->assertEquals($productName, $productVO->getName());
        $this->assertEquals($productPrice, $productVO->getPrice());
        $this->assertEquals($productStock, $productVO->getStock());
    }

    public function testInvalidData_invalidUuid(): void
    {
        $id = 'invalid-uuid';
        $productName = 'productName';
        $productPrice = 1199;
        $productStock= 0;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductEntity(
            $id,
            $productName,
            $productPrice,
            $productStock
        );
    }

    public function testValidData_emptyName(): void
    {
        $id = uuid_create();
        $productName = '';
        $productPrice = 1199;
        $productStock= 0;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductEntity(
            $id,
            $productName,
            $productPrice,
            $productStock
        );
    }

    public function testValidData_priceZero(): void
    {
        $id = uuid_create();
        $productName = 'productName';
        $productPrice = 0;
        $productStock= 0;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductEntity(
            $id,
            $productName,
            $productPrice,
            $productStock
        );
    }


    public function testInvalidData_negativePrice(): void
    {
        $id = uuid_create();
        $productName = 'productName';
        $productPrice = -1;
        $productStock= 0;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductEntity(
            $id,
            $productName,
            $productPrice,
            $productStock
        );
    }

    public function testInvalidData_negativeStock(): void
    {
        $id = uuid_create();
        $productName = 'productName';
        $productPrice = 1199;
        $productStock= -1;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductEntity(
            $id,
            $productName,
            $productPrice,
            $productStock
        );
    }

}
