<?php

declare(strict_types=1);

namespace App\Tests\UseCase\CartProduct;

use App\DataFixtures\AppFixtures;
use App\Entity\Cart;
use App\Entity\CartProduct;
use App\Repository\CartRepository;
use App\UseCase\CartProduct\CartProductCreator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartProductCreatorTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CartProductCreator $cartProductCreator;
    private CartRepository $cartRepository;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartProductCreator = self::$container->get(CartProductCreator::class);
        $this->cartRepository = self::$container->get(CartRepository::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function test_addProductNotInCart(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $product5 = $this->fixtures->getProductReference(AppFixtures::PRODUCT5_REFERENCE);
        $quantity = 3;

        $this->cartProductCreator->create($quantity, $cart->getId(), $product5->getId());
        $cartSaved = $this->cartRepository->find($cart->getId());
        $cartProduct = $this->getProductFromCart($cartSaved, $product5->getId());

        $this->assertNotNull($cartProduct);
        $this->assertEquals($quantity, $cartProduct->getQuantity());
    }

    public function test_addProductAlreadyInCart(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartProduct1 = $this->fixtures->getCartProductReference(AppFixtures::CART_PRODUCT1_REFERENCE);
        $quantity = 3;

        $this->cartProductCreator->create($quantity, $cart->getId(), $cartProduct1->getProductId());
        $cartSaved = $this->cartRepository->find($cart->getId());
        $cartProduct = $this->getProductFromCart($cartSaved, $cartProduct1->getProductId());

        $this->assertNotNull($cartProduct);
        $expectedQuantity = $quantity + $cartProduct1->getQuantity();
        $this->assertEquals($expectedQuantity, $cartProduct->getQuantity());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_addProductInNonExistingCart(): void
    {
        $product5 = $this->fixtures->getProductReference(AppFixtures::PRODUCT5_REFERENCE);
        $quantity = 3;

        $this->expectException(ResourceNotFoundException::class);
        $this->cartProductCreator->create($quantity, uuid_create(), $product5->getId());
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function test_addNonExistingProduct(): void
    {
        $cart = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $quantity = 3;

        $this->expectException(ResourceNotFoundException::class);
        $this->cartProductCreator->create($quantity, $cart->getId(), uuid_create());
    }

    /**
     * @param Cart $cart
     * @param string $productId
     *
     * @return CartProduct|null
     */
    private function getProductFromCart(Cart $cart, string $productId): ?CartProduct
    {
        foreach($cart->getCartProducts() as $cartProduct) {
            if ($cartProduct->getProductId() === $productId) {
                return $cartProduct;
            }
        }

        return null;
    }
}
