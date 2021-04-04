<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Logic\Converter\CartEntityToJsonObject;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartEntityToJsonObjectTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CartEntityToJsonObject $cartEntityToJsonObject;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartEntityToJsonObject = self::$container->get(CartEntityToJsonObject::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function test(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartObject = $this->cartEntityToJsonObject->convert($cart);

        $this->assertEquals($cart->getId(), $cartObject->id);
        $this->assertEquals($cart->getCreationDate()->format('d.m.Y H:i:s'), $cartObject->creationDate);
        $this->assertEquals($cart->getLastModified()->format('d.m.Y H:i:s'), $cartObject->lastModified);
        $this->assertEquals($this->getCartPrice($cart), $cartObject->cartPrice);
        $this->assertAllCartProductsEquals($cart->getCartProducts(), $cartObject->cartProducts);
    }

    /**
     * @param Cart $cart
     *
     * @return int
     */
    private function getCartPrice(Cart $cart)
    {
        $price = 0;
        foreach($cart->getCartProducts() as $cartProduct) {
            $price += $cartProduct->getCartProductPrice();
        }

        return $price / 100;
    }

    /**
     * @param Collection $cartProductEntities
     * @param array $cartProductObjects
     */
    private function assertAllCartProductsEquals(Collection $cartProductEntities, array $cartProductObjects): void
    {
        $this->assertEquals($cartProductEntities->count(), count($cartProductObjects));
        $cartProductEntitiesDictionary = $this->indexCartProductEntitiesById($cartProductEntities);
        foreach($cartProductObjects as $cartProductObject) {
            $cartProductEntity = $cartProductEntitiesDictionary[$cartProductObject->id];
            $this->assertNotNull($cartProductEntity);
            $this->assertCartProductsEqual($cartProductEntity, $cartProductObject);
        }
    }

    /**
     * @param CartProduct $cartProductEntity
     * @param object $cartProductObject
     */
    private function assertCartProductsEqual(CartProduct $cartProductEntity, object $cartProductObject)
    {
        $this->assertEquals($cartProductEntity->getId(), $cartProductObject->id);
        $this->assertEquals($cartProductEntity->getQuantity(), $cartProductObject->quantity);
        $this->assertEquals($cartProductEntity->getProductId(), $cartProductObject->productId);
        $this->assertEquals($cartProductEntity->getProductName(), $cartProductObject->productName);
        $this->assertEquals($cartProductEntity->getProductPrice() / 100, $cartProductObject->productPrice);
        $this->assertEquals($cartProductEntity->getProductStock(), $cartProductObject->productStock);
        $this->assertEquals($cartProductEntity->getCartProductPrice() / 100, $cartProductObject->cartProductPrice);
    }

    /**
     * @param Collection $cartProductEntities
     *
     * @return array
     */
    private function indexCartProductEntitiesById(Collection $cartProductEntities): array
    {
        $result = [];
        foreach($cartProductEntities as $cartProductEntity) {
            $result[$cartProductEntity->getId()] = $cartProductEntity;
        }

        return $result;
    }
}