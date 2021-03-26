<?php

declare(strict_types=1);

namespace App\Tests\DomainObject\ValueObject;

use App\DomainObject\ValueObject\CartProductVO;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartProductVoTest extends KernelTestCase
{
    public function testValidData(): void
    {
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );

        $this->assertEquals($quantity, $cartProductVO->getQuantity());
        $this->assertEquals($quantity*$productPrice, $cartProductVO->getCartProductPrice());
        $this->assertEquals($productName, $cartProductVO->getProductName());
        $this->assertEquals($productPrice, $cartProductVO->getProductPrice());
        $this->assertEquals($cartId, $cartProductVO->getCartId());
        $this->assertEquals($productId, $cartProductVO->getProductId());
    }

    public function testInvalidData_notEnoughStock(): void
    {
        $quantity = 2;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_quantityZero(): void
    {
        $quantity = 0;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_negativeQuantity(): void
    {
        $quantity = -1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_emptyProductName(): void
    {
        $quantity = 1;
        $productName = '';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_productPriceZero(): void
    {
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 0;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_negativeProductPrice(): void
    {
        $quantity = 1;
        $productName = 'productName';
        $productPrice = -1;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_productStockZero(): void
    {
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 0;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_negativeProductStock(): void
    {
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = -1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_invalidCartId(): void
    {
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = -1;
        $cartId = 'invalid-uuid';
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_invalidProductId(): void
    {
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = -1;
        $cartId = uuid_create();
        $productId = 'invalid-uuid';

        $this->expectException(InvalidArgumentException::class);

        $cartProductVO = new CartProductVO(
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }
}
