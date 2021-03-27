<?php

namespace App\Tests\Logic\Converter\Traits;

use App\DomainObject\Entity\CartProductEntity;
use App\Entity\CartProductDE;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Tightenco\Collect\Support\Collection as TightencoCollection;

trait AssertCartProductDesEqualsCartProductEntities
{
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
     * @param CartProductDE $cartProductDE
     * @param CartProductEntity $cartProductEntity
     */
    private function assertCartProductsEqual(CartProductDE $cartProductDE, CartProductEntity $cartProductEntity): void
    {
        $this->assertEquals($cartProductDE->getId(), $cartProductEntity->getId());
        $this->assertEquals($cartProductDE->getProductName(), $cartProductEntity->getProductName());
        $this->assertEquals($cartProductDE->getProductPrice(), $cartProductEntity->getProductPrice());
        $this->assertEquals($cartProductDE->getQuantity(), $cartProductEntity->getQuantity());
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
