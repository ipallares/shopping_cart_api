<?php

declare(strict_types=1);

namespace App\Tests\DomainObject\Entity;

use App\DomainObject\Entity\CartProductEntity;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartProductEntityTest extends KernelTestCase
{
    public function testValidData(): void
    {
        $id = uuid_create();
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $cartProductEntity = new CartProductEntity(
            $id,
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );

        $this->assertEquals($id, $cartProductEntity->getId());
        $this->assertEquals($quantity, $cartProductEntity->getQuantity());
        $this->assertEquals($quantity*$productPrice, $cartProductEntity->getCartProductPrice());
        $this->assertEquals($productName, $cartProductEntity->getProductName());
        $this->assertEquals($productPrice, $cartProductEntity->getProductPrice());
        $this->assertEquals($cartId, $cartProductEntity->getCartId());
        $this->assertEquals($productId, $cartProductEntity->getProductId());
    }

    public function testInvalidData_invalidId(): void
    {
        $id = 'invalid-uuid';
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = -1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

    public function testInvalidData_notEnoughStock(): void
    {
        $id = uuid_create();
        $quantity = 2;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 0;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = -1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 1;
        $productName = '';
        $productPrice = 1199;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 0;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 1;
        $productName = 'productName';
        $productPrice = -1;
        $productStock = 1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = 0;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = -1;
        $cartId = uuid_create();
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = -1;
        $cartId = 'invalid-uuid';
        $productId = uuid_create();

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
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
        $id = uuid_create();
        $quantity = 1;
        $productName = 'productName';
        $productPrice = 1199;
        $productStock = -1;
        $cartId = uuid_create();
        $productId = 'invalid-uuid';

        $this->expectException(InvalidArgumentException::class);

        $cartProductEntity = new CartProductEntity(
            $id,
            $quantity,
            $productName,
            $productPrice,
            $productStock,
            $cartId,
            $productId
        );
    }

}