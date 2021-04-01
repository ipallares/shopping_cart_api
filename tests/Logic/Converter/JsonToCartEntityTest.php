<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Logic\Converter\CartEntityToJson;
use App\Logic\Converter\JsonToCartEntity;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class JsonToCartEntityTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private JsonToCartEntity $jsonToCartEntity;
    private CartEntityToJson $cartEntityToJson;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->jsonToCartEntity = self::$container->get(JsonToCartEntity::class);
        $this->cartEntityToJson = self::$container->get(CartEntityToJson::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testConvertNewCart(): void
    {
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);

        $cartObject = $this->getNewCartObject();
        $convertedCart = $this->jsonToCartEntity->convert(json_encode($cartObject));

        $this->assertEmpty($convertedCart->getCartProducts());
        // In an extremely edgy case this validation might be randomly wrong
        $this->assertEquals(
            $convertedCart->getCreationDate()->format('d.m.Y H:i:s'),
            $convertedCart->getLastModified()->format('d.m.Y H:i:s')
        );

        // Add one product
        $cartObject->id = $convertedCart->getId();
        $cartObject->creationDate = $convertedCart->getCreationDate();
        $cartObject->lastModified = $convertedCart->getLastModified();
        $quantity = 1;
        $cartObject->cartProducts[] = $this->getCartProductObject($quantity, $product1);
        $convertedCart = $this->jsonToCartEntity->convert(json_encode($cartObject));

        $this->assertCount(1, $convertedCart->getCartProducts());
        $this->assertValidCartProduct($quantity, $product1, $convertedCart->getCartProducts()->current());

        // Remove the product
        $cartObject->cartProducts = [];
        $convertedCart = $this->jsonToCartEntity->convert(json_encode($cartObject));
        $this->assertEmpty($convertedCart->getCartProducts());
    }

    public function testConvertExistingCart(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $product2 = $this->fixtures->getProductReference(AppFixtures::PRODUCT2_REFERENCE);
        $product3 = $this->fixtures->getProductReference(AppFixtures::PRODUCT3_REFERENCE);

        $cartObject = $this->getExistingCartObject($cart);
        $quantity1 = 3;
        $quantity2 = 5;
        $quantity3 = 1;
        $cartObject->cartProducts[] = $this->getCartProductObject($quantity1, $product1);
        $cartObject->cartProducts[] = $this->getCartProductObject($quantity2, $product2);
        $cartObject->cartProducts[] = $this->getCartProductObject($quantity3, $product3);

        $convertedCart = $this->jsonToCartEntity->convert(json_encode($cartObject));
        $cartProducts = $convertedCart->getCartProducts()->toArray();

        $cartProduct1 = $this->getCartProductByProductId($product1->getId(), $cartProducts);
        $this->assertValidCartProduct($quantity1, $product1, $cartProduct1);

        $cartProduct2 = $this->getCartProductByProductId($product2->getId(), $cartProducts);
        $this->assertValidCartProduct($quantity2, $product2, $cartProduct2);

        $cartProduct3 = $this->getCartProductByProductId($product3->getId(), $cartProducts);
        $this->assertValidCartProduct($quantity3, $product3, $cartProduct3);
    }

    /**
     * @param string $productId
     * @param array $cartProducts
     *
     * @return CartProduct|null
     */
    private function getCartProductByProductId(string $productId, array $cartProducts): ?CartProduct
    {
        foreach($cartProducts as $cartProduct) {
            if ($productId === $cartProduct->getProductId()) {
                return $cartProduct;
            }
        }

        return null;
    }

    /**
     * Assert the received CartProduct has the proper info based on the quantity and Product it was created from.
     *
     * @param int $quantity
     * @param Product $product
     * @param CartProduct $cartProduct
     */
    private function assertValidCartProduct(int $quantity, Product $product, CartProduct $cartProduct)
    {
        $this->assertEquals($quantity, $cartProduct->getQuantity());
        $this->assertEquals($product->getId(), $cartProduct->getProductId());
        $this->assertEquals($product->getName(), $cartProduct->getProductName());
        $this->assertEquals($product->getPrice(), $cartProduct->getProductPrice());
        $this->assertEquals($product->getStock(), $cartProduct->getProductStock());
        $this->assertEquals($quantity*$product->getPrice(), $cartProduct->getCartProductPrice());
    }

    /**
     * Builds an object for an existing Cart (meaning it will have its id, creationDate and lastModified date) with format
     * expected by the API Request.
     *
     * @param Cart $cart
     *
     * @return object
     */
    private function getExistingCartObject(Cart $cart): object
    {
        $cartObject = new StdClass();
        $cartObject->id = $cart->getId();
        $cartObject->cartProducts = [];

        return $cartObject;
    }

    /**
     * Builds an object for a not yet existing Cart (no id, creationDate, lastModified or cartProducts) with format
     * expected by the API Request.
     *
     * @return object
     */
    private function getNewCartObject(): object
    {
        $cartObject = new StdClass();
        $cartObject->cartProducts = [];

        return $cartObject;
    }

    /**
     * Builds an object for a CartProduct based on the quantity and product received as parameters, with format
     * expected by the API Request.
     *
     * @param int $quantity
     * @param Product $product
     *
     * @return object
     */
    private function getCartProductObject(int $quantity, Product $product): object
    {
        $productObject = new StdClass();
        $productObject->quantity = $quantity;
        $productObject->productId = $product->getId();

        return $productObject;
    }
}
