<?php

declare(strict_types=1);

namespace App\Logic\Converter;

use App\DomainObject\Entity\CartEntity;
use App\Entity\CartDE;
use Tightenco\Collect\Support\Collection;

class CartDeToEntity
{
    private CartProductDeToEntity $cartProductDeToEntity;

    public function __construct(CartProductDeToEntity $cartProductDeToEntity)
    {
        $this->cartProductDeToEntity = $cartProductDeToEntity;
    }

    /**
     * @param CartDE                  $cart
     *
     * @return CartEntity
     */
    public function convert(CartDE $cart): CartEntity
    {
        $cartProductEntities = $this->getCartProductEntities($cart);

        return new CartEntity($cart->getId(), $cart->getCreationDate(), $cart->getLastModified(), $cartProductEntities);
    }

    private function getCartProductEntities(CartDE $cart): Collection
    {
        $cartProducts = collect($cart->getCartProducts()->toArray());

        return $cartProducts->map([$this->cartProductDeToEntity, 'convert']);
    }

}
