<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\DomainObject\Entity\CartEntity;
use App\DomainObject\Entity\CartProductEntity;
use App\Logic\Converter\CartEntityToDE;
use App\Tests\Logic\Converter\Traits\AssertCartProductDesEqualsCartProductEntities;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tightenco\Collect\Support\Collection;

class CartEntityToDeTest extends KernelTestCase
{
    use FixturesTrait;
    use AssertCartProductDesEqualsCartProductEntities;

    private AppFixtures $fixtures;
    private CartEntityToDE $cartEntityToDE;
    private EntityManagerInterface $manager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartEntityToDE = self::$container->get(CartEntityToDE::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testConversion(): void
    {
        $cartId = uuid_create();
        $creationDate = $lastModified = new DateTime();
        $cartProductEntities = $this->getCartProductEntities();

        $cartEntity = new CartEntity(
            $cartId,
            $creationDate,
            $lastModified,
            $cartProductEntities
        );

        $cartDE = $this->cartEntityToDE->convert($cartEntity);

        $this->assertEquals($cartId, $cartDE->getId());
        $this->assertEquals($creationDate, $cartDE->getCreationDate());
        $this->assertEquals($lastModified, $cartDE->getLastModified());
        $this->assertAllCartProductsEqual($cartDE->getCartProducts(), $cartEntity->getCartProducts());
    }

    private function getCartProductEntities(): Collection
    {
        $quantity1 = 2;
        $productDE1 = $this->fixtures->getProductReference(AppFixtures::PRODUCT1_REFERENCE);
        $product1Id = $productDE1->getId();
        $product1Name = $productDE1->getName();
        $product1Price = $productDE1->getPrice();
        $product1Stock = $productDE1->getStock();

        $productEntity1 = new CartProductEntity(
            uuid_create(),
            $quantity1,
            $product1Name,
            $product1Price,
            $product1Stock,
            $product1Id
        );

        $quantity2 = 2;
        $productDE2 = $this->fixtures->getProductReference(AppFixtures::PRODUCT2_REFERENCE);
        $product2Id = $productDE2->getId();
        $product2Name = $productDE2->getName();
        $product2Price = $productDE2->getPrice();
        $product2Stock = $productDE2->getStock();

        $productEntity2 = new CartProductEntity(
            uuid_create(),
            $quantity2,
            $product2Name,
            $product2Price,
            $product2Stock,
            $product2Id
        );

        return new Collection([$productEntity1, $productEntity2]);
    }
}
