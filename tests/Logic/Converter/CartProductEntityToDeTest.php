<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\DomainObject\Entity\CartProductEntity;
use App\Logic\Converter\CartProductEntityToDE;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartProductEntityToDeTest extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private CartProductEntityToDE $cartProductEntityToDE;
    private EntityManagerInterface $manager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartProductEntityToDE = self::$container->get(CartProductEntityToDE::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testConvert(): void
    {
        $id = uuid_create();
        $quantity = 2;
        $productDE = $this->fixtures->getProductReference(AppFixtures::PRODUCT2_REFERENCE);

        $cartProductEntity = new CartProductEntity(
            $id,
            $quantity,
            $productDE->getName(),
            $productDE->getPrice(),
            $productDE->getStock(),
            $productDE->getId()
        );

        $cartProductDE = $this->cartProductEntityToDE->convert($cartProductEntity);

        $this->assertEquals($cartProductEntity->getId(), $cartProductDE->getId());
        $this->assertEquals($cartProductEntity->getQuantity(), $cartProductDE->getQuantity());
        $this->assertEquals($cartProductEntity->getProductName(), $cartProductDE->getProductName());
        $this->assertEquals($cartProductEntity->getProductPrice(), $cartProductDE->getProductPrice());
        $this->assertEquals($cartProductEntity->getProductStock(), $cartProductDE->getProductStock());
        $this->assertEquals($cartProductEntity->getProductId(), $cartProductDE->getProductId());

    }
}
