<?php

declare(strict_types=1);

namespace App\Tests\UseCase\CartProduct;

use App\DataFixtures\AppFixtures;
use App\Repository\CartProductRepository;
use App\UseCase\CartProduct\CartProductDeleter;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CartProductDeleterTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private EntityManagerInterface $manager;
    private CartProductDeleter $cartProductDeleter;
    private CartProductRepository $cartProductRepository;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartProductDeleter = self::$container->get(CartProductDeleter::class);
        $this->cartProductRepository = self::$container->get(CartProductRepository::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function test_removeExisting(): void
    {
        $cartProduct = $this->fixtures->getCartProductReference(AppFixtures::CART_PRODUCT1_REFERENCE);
        $cartProductId = $cartProduct->getId();
        $found = true;
        try{
            $cartProduct = $this->cartProductRepository->findWithCertainty($cartProductId);
        } catch(ResourceNotFoundException $e) {
            $found = false;
        }
        $this->assertTrue($found);
        $this->cartProductDeleter->delete($cartProduct->getId());
        $cartProduct = $this->cartProductRepository->find($cartProductId);
        $this->assertNull($cartProduct);
    }

    public function test_removeNonExisting(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->cartProductDeleter->delete(uuid_create());
    }
}
