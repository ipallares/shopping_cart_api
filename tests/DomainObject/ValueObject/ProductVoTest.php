<?php

declare(strict_types=1);

namespace App\Tests\DomainObject\ValueObject;

use App\DomainObject\ValueObject\ProductVO;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductVoTest extends TestCase
{
    public function testValidData(): void
    {
        $productName = 'productName';
        $productPrice = 1199;
        $productStock= 0;

        $productVO = new ProductVO(
            $productName,
            $productPrice,
            $productStock
        );

        $this->assertEquals($productName, $productVO->getName());
        $this->assertEquals($productPrice, $productVO->getPrice());
        $this->assertEquals($productStock, $productVO->getStock());
    }

    public function testInvalidData_emptyName(): void
    {
        $productName = '';
        $productPrice = 1199;
        $productStock= 10;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductVO(
            $productName,
            $productPrice,
            $productStock
        );
    }

    public function testInvalidData_priceZero(): void
    {
        $productName = 'productName';
        $productPrice = 0;
        $productStock= 0;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductVO(
            $productName,
            $productPrice,
            $productStock
        );
    }

    public function testInvalidData_negativePrice(): void
    {
        $productName = 'productName';
        $productPrice = -1;
        $productStock= 0;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductVO(
            $productName,
            $productPrice,
            $productStock
        );
    }

    public function testInvalidData_negativeStock(): void
    {
        $productName = 'productName';
        $productPrice = 1199;
        $productStock= -1;

        $this->expectException(InvalidArgumentException::class);

        $productVO = new ProductVO(
            $productName,
            $productPrice,
            $productStock
        );
    }
}
