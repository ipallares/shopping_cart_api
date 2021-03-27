<?php

declare(strict_types=1);

namespace App\Tests\Logic\Converter;

use App\DataFixtures\AppFixtures;
use App\DomainObject\Entity\CartProductEntity;
use App\Entity\CartProductDE;
use App\Logic\Converter\CartDeToEntity;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\EntityManagerInterface;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tightenco\Collect\Support\Collection as TightencoCollection;

class CartDeToEntityTest  extends KernelTestCase
{
    use FixturesTrait;

    private AppFixtures $fixtures;
    private CartDeToEntity $cartDeToEntity;
    private EntityManagerInterface $manager;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->cartDeToEntity = self::$container->get(CartDeToEntity::class);
        $this->manager = self::$container->get('doctrine.orm.entity_manager');
        $this->loadFixtures([AppFixtures::class]);
        $this->fixtures = self::$container->get(AppFixtures::class);
    }

    public function testConvert(): void
    {
        $cartDE = $this->fixtures->getCartReference(AppFixtures::CART_REFERENCE);
        $cartEntity = $this->cartDeToEntity->convert($cartDE);

        $this->assertEquals($cartDE->getId(), $cartEntity->getId());
        $this->assertEquals($cartDE->getCreationDate()->getTimestamp(), $cartEntity->getCreationDate()->getTimestamp());
        $this->assertEquals($cartDE->getLastModified()->getTimestamp(), $cartEntity->getLastModified()->getTimestamp());
        $this->assertAllCartProductsEqual($cartDE->getCartProducts(), $cartEntity->getCartProducts());
    }

    /**
     * @param DoctrineCollection $cartProductDes
     * @param TightencoCollection $cartProductEntities
     */
    private function assertAllCartProductsEqual(DoctrineCollection $cartProductDes, TightencoCollection $cartProductEntities): void
    {
        $this->assertCount($cartProductEntities->count(), $cartProductDes);
        $cartProductEntitiesDictionary = $this->indexCartProductEntitiesById($cartProductEntities);

        foreach($cartProductDes as $cartProductDE) {
            $cartProductEntity = $cartProductEntitiesDictionary->get($cartProductDE->getId());
            $this->assertNotNull($cartProductEntity);
            $this->assertCartProductsEqual($cartProductDE, $cartProductEntity);
        }
    }

    /**
     * @param CartProductDE $productDE
     * @param CartProductEntity $productEntity
     */
    private function assertCartProductsEqual(CartProductDE $productDE, CartProductEntity $productEntity): void
    {
        $this->assertEquals($productDE->getId(), $productEntity->getId());
        $this->assertEquals($productDE->getProductName(), $productEntity->getProductName());
        $this->assertEquals($productDE->getProductPrice(), $productEntity->getProductPrice());
        $this->assertEquals($productDE->getQuantity(), $productEntity->getQuantity());
        $this->assertEquals($productDE->getCart()->getId(), $productEntity->getCartId());
    }

    /**
     * @param TightencoCollection $cartProductEntities
     *
     * @return TightencoCollection
     */
    private function indexCartProductEntitiesById(TightencoCollection $cartProductEntities): TightencoCollection
    {
        return $cartProductEntities->mapWithKeys(
            function(CartProductEntity $cartProductEntity): array {
                return [$cartProductEntity->getId() => $cartProductEntity];
            }
        );
    }
}
