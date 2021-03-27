<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\Logic\Converter\CartProductDeToEntity;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartProductDeToEntityTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private CartProductDeToEntity $cartProductDeToEntity;
    private EntityManagerInterface $manager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartProductDeToEntity = self::$container->get(CartProductDeToEntity::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testConvert(): void
    {
        $cartProduct = $this->fixtures->getCartProductReference(AppFixtures::CART_PRODUCT1_REFERENCE);
        $cartEntity = $this->cartProductDeToEntity->convert($cartProduct);

        $this->assertEquals($cartEntity->getId(), $cartProduct->getId());
        $this->assertEquals($cartEntity->getQuantity(), $cartProduct->getQuantity());
        $this->assertEquals($cartEntity->getProductId(), $cartProduct->getProductId());
        $this->assertEquals($cartEntity->getProductName(), $cartProduct->getProductName());
        $this->assertEquals($cartEntity->getProductPrice(), $cartProduct->getProductPrice());
        $this->assertEquals($cartEntity->getProductStock(), $cartProduct->getProductStock());

    }
}
