<?php

declare(strict_types=1);

namespace App\Tests\Logic\Validator\CartProduct;

use App\DataFixtures\AppFixtures;
use App\Logic\Validator\CartProduct\CreateCartProductValidator;
use App\UseCase\CartProduct\CartProductCreator;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCartProductValidatorTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CartProductCreator $cartProductCreator;
    private CreateCartProductValidator $createCartProductValidator;


    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->createCartProductValidator = self::$container->get(CreateCartProductValidator::class);
        $this->cartProductCreator = self::$container->get(CartProductCreator::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testValid_quantitySmallerThanStock(): void
    {
        $emptyCart = $this->fixtures->getCartReference(AppFixtures::CART_EMPTY_REFERENCE);
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $quantity = $product1->getStock() - 1;
        $this->assertValidQuantity($quantity, $emptyCart->getId(), $product1->getId());
    }

    public function testValid_quantityEqualsStock(): void
    {
        $emptyCart = $this->fixtures->getCartReference(AppFixtures::CART_EMPTY_REFERENCE);
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $quantity = $product1->getStock();
        $this->assertValidQuantity($quantity, $emptyCart->getId(), $product1->getId());
    }

    public function testValid_quantityBiggerThanStock(): void
    {
        $emptyCart = $this->fixtures->getCartReference(AppFixtures::CART_EMPTY_REFERENCE);
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $quantity = $product1->getStock() + 1;
        $this->assertInvalidQuantity($quantity, $emptyCart->getId(), $product1->getId());
    }

    public function testValid_existingProductQuantityPlusNewQuantityBiggerThanStock(): void
    {
        $emptyCart = $this->fixtures->getCartReference(AppFixtures::CART_EMPTY_REFERENCE);
        $product1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $quantity = $product1->getStock();
        $this->cartProductCreator->create($quantity, $emptyCart->getId(), $product1->getId());

        $quantity = 1;
        $this->assertInvalidQuantity($quantity, $emptyCart->getId(), $product1->getId());
    }

    public function assertValidQuantity(int $quantity, string $cartId, string $productId): void
    {
        $valid = true;
        try {
            $this->cartProductCreator->create($quantity, $cartId, $productId);
        } catch (InvalidArgumentException $e) {
            $valid = false;
        }

        $this->assertTrue($valid);
    }

    public function assertInvalidQuantity(int $quantity, string $cartId, string $productId): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cartProductCreator->create($quantity, $cartId, $productId);
    }
}
