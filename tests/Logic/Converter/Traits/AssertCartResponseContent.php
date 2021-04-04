<?php

namespace App\Tests\Logic\Converter\Traits;

use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\Collection;

trait AssertCartResponseContent
{
    /**
     * @param object $cartResponse
     */
    private function assertCartResponse_expectedFields(object $cartResponse): void
    {
        $this->assertObjectHasAttribute('cartPrice', $cartResponse);

        $this->assertObjectHasAttribute('cartProducts', $cartResponse);
        $this->assertTrue(is_array($cartResponse->cartProducts));
        $this->assertCartProducts_expectedFields($cartResponse->cartProducts);

        $this->assertObjectHasAttribute('creationDate', $cartResponse);
        $this->assertNotEmpty($cartResponse->creationDate);

        $this->assertObjectHasAttribute('id', $cartResponse);
        $this->assertNotEmpty($cartResponse->id);

        $this->assertObjectHasAttribute('lastModified', $cartResponse);
        $this->assertNotEmpty($cartResponse->lastModified);
    }

    /**
     * @param array<int, object> $cartProductObjects
     */
    private function assertCartProducts_expectedFields(array $cartProductObjects): void
    {
        foreach ($cartProductObjects as $cartProductObject) {
            $this->assertCartProductResponse_expectedFields($cartProductObject);
        }
    }

    /**
     * @param object $cartProductResponse
     */
    private function assertCartProductResponse_expectedFields(object $cartProductResponse): void
    {
        $this->assertObjectHasAttribute('cartProductPrice', $cartProductResponse);
        $this->assertObjectHasAttribute('id', $cartProductResponse);
        $this->assertObjectHasAttribute('productId', $cartProductResponse);
        $this->assertObjectHasAttribute('productName', $cartProductResponse);
        $this->assertObjectHasAttribute('productPrice', $cartProductResponse);
        $this->assertObjectHasAttribute('productStock', $cartProductResponse);
        $this->assertObjectHasAttribute('quantity', $cartProductResponse);
    }

    /**
     * @param object $cartResponse
     */
    private function assertCartResponse_matchesCartEntity(object $cartResponse): void
    {
        $cartRepository = static::$container->get(CartRepository::class);
        /** @var Cart $cart */
        $cart = $cartRepository->find($cartResponse->id);

        $this->assertEquals($cart->getCartPrice() / 100, $cartResponse->cartPrice);
        $this->assertEquals($cart->getCartProducts()->count(), count($cartResponse->cartProducts));
        $this->assertCartProducts_matchCartProductEntities($cartResponse->cartProducts, $cart->getCartProducts());


        $this->assertEquals($cart->getCreationDate()->format('d.m.Y H:i:s'), $cartResponse->creationDate);
        $this->assertEquals($cart->getId(), $cartResponse->id);
        $this->assertEquals($cart->getLastModified()->format('d.m.Y H:i:s'), $cartResponse->lastModified);
    }

    /**
     * @param array<int, object> $cartProductObjects
     * @param Collection<int, CartProduct> $cartProductEntities
     */
    private function assertCartProducts_matchCartProductEntities(array $cartProductObjects, Collection $cartProductEntities): void
    {
        $this->assertEquals(count($cartProductObjects), $cartProductEntities->count());
        $indexedCartProductEntities = $this->indexArrayByIds($cartProductEntities);
        foreach ($cartProductObjects as $cartProductObject) {
            $this->assertCartProductResponse_matchCartProductEntity(
                $cartProductObject,
                $indexedCartProductEntities[$cartProductObject->id]
            );
        }
    }

    /**
     * @param object $cartProductResponse
     * @param CartProduct $cartProduct
     */
    private function assertCartProductResponse_matchCartProductEntity(object $cartProductResponse, CartProduct $cartProduct): void
    {
        $this->assertEquals($cartProduct->getCartProductPrice() / 100, $cartProductResponse->cartProductPrice);
        $this->assertEquals($cartProduct->getProductId(), $cartProductResponse->productId);
        $this->assertEquals($cartProduct->getProductName(), $cartProductResponse->productName);
        $this->assertEquals($cartProduct->getProductPrice() / 100, $cartProductResponse->productPrice);
        $this->assertEquals($cartProduct->getProductStock(), $cartProductResponse->productStock);
        $this->assertEquals($cartProduct->getQuantity(), $cartProductResponse->quantity);
    }

    /**
     * @param object $cartProductResponse
     * @param Product $product
     */
    private function assertCartProductResponse_matchProductEntity(object $cartProductResponse, Product $product): void
    {
        $expectedCartProductPrice = $product->getPrice() / 100 * $cartProductResponse->quantity;
        $this->assertEquals($expectedCartProductPrice, $cartProductResponse->cartProductPrice);
        $this->assertEquals($product->getId(), $cartProductResponse->productId);
        $this->assertEquals($product->getName(), $cartProductResponse->productName);
        $this->assertEquals($product->getPrice() / 100, $cartProductResponse->productPrice);
        $this->assertEquals($product->getStock(), $cartProductResponse->productStock);
    }

    /**
     * @param Collection<int, CartProduct> $cartProductEntities
     *
     * @return array<string, CartProduct>
     */
    private function indexArrayByIds(Collection $cartProductEntities): array
    {
        $result = [];
        foreach($cartProductEntities as $cartProductEntity) {
            $result[$cartProductEntity->getId()] = $cartProductEntity;
        }

        return $result;
    }
}
