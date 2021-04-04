<?php

declare(strict_types=1);

namespace App\Tests\UseCase;

use App\DataFixtures\AppFixtures;
use App\Entity\Cart;
use App\Entity\Product;
use App\Logic\Converter\CartEntityToJson;
use App\Repository\CartRepository;
use App\UseCase\CartUpdater;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartUpdaterTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CartUpdater $cartUpdater;
    private CartRepository $cartRepository;
    private CartEntityToJson $cartEntityToJson;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartUpdater = self::$container->get(CartUpdater::class);
        $this->cartRepository = self::$container->get(CartRepository::class);
        $this->cartEntityToJson = self::$container->get(CartEntityToJson::class);

        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testUpdate_addOneProduct(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $initialNumberProducts = $cart->getNumberOfProducts();
        $initialCartPrice = $cart->getCartPrice();

        $cartJson = $this->cartEntityToJson->convert($cart);
        $cartObject = json_decode($cartJson);
        $quantity = 2;
        $product5 = $this->fixtures->getProductReference(AppFixtures::PRODUCT5_REFERENCE);
        $cartObject->cartProducts[] = $this->getCartProductObject($quantity, $product5);

        $savedCartObject = $this->cartUpdater->update(json_encode($cartObject));
        $updatedCart = $this->cartRepository->findWithCertainty($savedCartObject->id);

        $this->assertEquals(
            $initialNumberProducts + 1,
            $updatedCart->getNumberOfProducts()
        );
        $this->assertEquals(
            $initialCartPrice + $quantity*$product5->getPrice(),
            $updatedCart->getCartPrice()
        );
    }


    public function testUpdate_removeOneProduct(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $initialNumberProducts = $cart->getNumberOfProducts();
        $product3 = $this->fixtures->getProductReference(AppFixtures::PRODUCT3_REFERENCE);

        $cart = $this->removeProduct($cart, $product3);
        $this->assertEquals($initialNumberProducts - 1, $cart->getNumberOfProducts());

        $cartJson = $this->cartEntityToJson->convert($cart);
        $savedCartObject = $this->cartUpdater->update($cartJson);
        $updatedCart = $this->cartRepository->findWithCertainty($savedCartObject->id);

        $this->assertEquals(
            $initialNumberProducts - 1,
            $updatedCart->getNumberOfProducts()
        );
    }

    public function testUpdate_removeAllProducts(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cart->setCartProducts(new ArrayCollection());
        $this->assertEquals(0, $cart->getNumberOfProducts());

        $cartJson = $this->cartEntityToJson->convert($cart);
        $savedCartObject = $this->cartUpdater->update($cartJson);
        $updatedCart = $this->cartRepository->findWithCertainty($savedCartObject->id);

        $this->assertEquals(0, $updatedCart->getNumberOfProducts());
    }

    private function removeProduct(Cart $cart, Product $product): Cart
    {
        $cartProducts = $cart->getCartProducts();
        $result = new ArrayCollection();
        foreach($cartProducts as $cartProduct) {
            if ($cartProduct->getProductId() !== $product->getId()) {
                $result[] = $cartProduct;
            }
        }
        $cart->setCartProducts($result);

        return $cart;
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
        $cartProductObject = new StdClass();
        $cartProductObject->quantity = $quantity;
        $cartProductObject->productName = $product->getName();
        $cartProductObject->productPrice = $product->getPrice();
        $cartProductObject->productStock = $product->getStock();
        $cartProductObject->productId = $product->getId();
        $cartProductObject->cartProductPrice = $quantity*$product->getPrice();

        return $cartProductObject;
    }
}
