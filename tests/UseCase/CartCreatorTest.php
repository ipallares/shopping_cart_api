<?php

declare(strict_types=1);

namespace App\Tests\UseCase;

use App\DataFixtures\AppFixtures;
use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Entity\Product;
use App\Repository\CartRepository;
use App\UseCase\CartCreator;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use OutOfBoundsException;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartCreatorTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CartCreator $cartCreator;
    private CartRepository $cartRepository;


    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartCreator = self::$container->get(CartCreator::class);
        $this->cartRepository = self::$container->get(CartRepository::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testCreate_cartWithOneProduct(): void
    {
        $newCartObject = $this->getNewCartObject();
        $quantity = 1;
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $newCartObject->cartProducts[] = $this->getCartProductObject($quantity, $product1);
        $savedCartJson = $this->cartCreator->create(json_encode($newCartObject));
        $savedCartObject = json_decode($savedCartJson);

        $this->assertCartCreated($savedCartObject);
        $this->assertExpectedCartProduct($savedCartObject->id, $quantity, $product1);
    }

    private function assertExpectedCartProduct(string $cartId, $quantity, Product $product): void
    {
        $cartEntity = $this->cartRepository->find($cartId);
        $this->assertCount(1, $cartEntity->getCartProducts());
        $cartProduct = $this->getCartProduct(0, $cartEntity);
        $this->assertEquals($quantity, $cartProduct->getQuantity());
        $this->assertEquals($product->getId(), $cartProduct->getProductId());
        $this->assertEquals($product->getName(), $cartProduct->getProductName());
        $this->assertEquals($product->getPrice(), $cartProduct->getProductPrice());
        $this->assertEquals($product->getStock(), $cartProduct->getProductStock());
        $this->assertEquals($quantity * $product->getPrice(), $cartProduct->getCartProductPrice());
    }

    private function getCartProduct(int $i, Cart $cart): CartProduct
    {
        if ($i >= $cart->getCartProducts()->count()) {
            throw new OutOfBoundsException('The Cart#' . $cart->getId() . " has no CartProduct in position '$i'");
        }

        return $cart->getCartProducts()->toArray()[$i];
    }

    private function assertIdIsSet(object $cart): void
    {
        $idIsSet = true;
        try {
            $cartId = $cart->id;
        } catch(Exception $e) {
            $idIsSet = false;
        }
        $this->assertTrue($idIsSet);
    }

    private function assertCartCreated(object $cart): void
    {
        $this->assertIdIsSet($cart);
        $cartExists = true;
        try {
            $cartEntity = $this->cartRepository->findWithCertainty($cart->id);
        } catch (ResourceNotFoundException $e) {
            $cartExists = false;
        }
        $this->assertTrue($cartExists);
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
